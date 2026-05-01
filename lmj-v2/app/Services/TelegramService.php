<?php

namespace App\Services;

use Telegram\Bot\Api;
use Exception;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $telegram;

    public function __construct()
    {
        try {
            $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        } catch (Exception $e) {
            Log::error("Telegram Init Error: " . $e->getMessage());
        }
    }

    public function sendMessage($chatId, $message)
    {
        if (!$this->telegram) return false;

        try {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            return true;
        } catch (Exception $e) {
            Log::error("Telegram Send Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendToAdmin($message)
    {
        return $this->sendMessage(env('TELEGRAM_ADMIN_CHAT_ID'), "⚠️ *ADMIN ALERT*\n\n" . $message);
    }

    public function sendToNoc($message)
    {
        return $this->sendMessage(env('TELEGRAM_NOC_CHAT_ID'), "🛠️ *NOC NOTIFICATION*\n\n" . $message);
    }
}
