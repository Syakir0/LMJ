<?php

namespace App\Services;

use App\Models\SystemSetting;
use Telegram\Bot\Api;
use Telegram\Bot\HttpClients\GuzzleHttpClient;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $telegram;
    protected $adminChatId;
    protected $nocChatId;

    public function __construct()
    {
        try {
            $token = $this->getSetting('telegram_bot_token', env('TELEGRAM_BOT_TOKEN'));
            $this->adminChatId = $this->getSetting('telegram_admin_chat_id', env('TELEGRAM_ADMIN_CHAT_ID'));
            $this->nocChatId = $this->getSetting('telegram_noc_chat_id', env('TELEGRAM_NOC_CHAT_ID'));

            if ($token) {
                // Create a Guzzle client with SSL verification disabled
                $client = new Client([
                    'verify' => false,
                    'timeout' => 30,
                ]);
                
                // Wrap it in the SDK's GuzzleHttpClient
                $httpClient = new GuzzleHttpClient($client);
                
                // Initialize the API with the custom HTTP client
                $this->telegram = new Api($token, false, $httpClient);
            }
        } catch (Exception $e) {
            Log::error("Telegram Init Error: " . $e->getMessage());
        }
    }

    protected function getSetting($key, $default = null)
    {
        try {
            $setting = SystemSetting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    public function sendMessage($chatId, $message)
    {
        if (!$this->telegram || !$chatId) return false;

        try {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            return true;
        } catch (Exception $e) {
            Log::error("Telegram Send Error: " . $e->getMessage());
            session(['tg_last_error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendToAdmin($message)
    {
        return $this->sendMessage($this->adminChatId, "⚠️ *ADMIN ALERT*\n\n" . $message);
    }

    public function sendToNoc($message)
    {
        return $this->sendMessage($this->nocChatId, "🛠️ *NOC NOTIFICATION*\n\n" . $message);
    }

    /**
     * Handle incoming updates from Telegram Webhook
     */
    public function handleUpdate($update)
    {
        if (isset($update['message'])) {
            $message = $update['message'];
            $chatId = $message['chat']['id'];

            if (isset($message['text']) && $message['text'] === '/start') {
                return $this->sendWelcomeMessage($chatId);
            } elseif (isset($message['contact'])) {
                return $this->handleContact($chatId, $message['contact']);
            }
        }
        return false;
    }

    protected function sendWelcomeMessage($chatId)
    {
        if (!$this->telegram) return false;

        try {
            $text = "👋 *Selamat datang di Bot Layanan Internet!*\n\n"
                  . "Silakan klik tombol di bawah untuk mendaftarkan akun Anda agar bisa menerima notifikasi tagihan otomatis.";
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => '📱 Bagikan Nomor HP', 'request_contact' => true]
                        ]
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ])
            ]);
            return true;
        } catch (Exception $e) {
            Log::error("Telegram Welcome Error: " . $e->getMessage());
            return false;
        }
    }

    protected function handleContact($chatId, $contact)
    {
        $phoneNumber = $contact['phone_number'];
        
        // Normalize input: remove all non-digits
        $cleanInput = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Get core number (remove 62 or 0 prefix)
        $coreNumber = $cleanInput;
        if (str_starts_with($cleanInput, '62')) {
            $coreNumber = substr($cleanInput, 2);
        } elseif (str_starts_with($cleanInput, '0')) {
            $coreNumber = substr($cleanInput, 1);
        }

        // Search in DB by stripping non-digits from the phone column as well
        // We use a trailing match to be safe
        $customer = \App\Models\Customer::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '+', ''), '-', ''), '.', '') LIKE ?", ["%$coreNumber"])->first();

        if ($customer) {
            $customer->update(['telegram_chat_id' => $chatId]);
            return $this->sendMessage($chatId, "✅ *Berhasil!*\n\nTerima kasih, {$customer->name}. Akun Anda telah terhubung. Anda akan menerima notifikasi tagihan secara otomatis di sini.");
        } else {
            Log::warning("Telegram Link Failed: Phone $phoneNumber (Core: $coreNumber) not found in DB.");
            return $this->sendMessage($chatId, "❌ *Maaf!*\n\nNomor HP `{$phoneNumber}` tidak terdaftar di sistem kami. Silakan hubungi Admin untuk memperbarui data Anda.");
        }
    }

    protected function normalizePhoneNumber($phone)
    {
        // Remove everything except numbers
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If it starts with 62, replace with 0
        if (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }
        
        // Take the last 10 digits to be safe with different prefixes
        return substr($phone, -10);
    }
}
