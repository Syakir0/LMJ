<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use Telegram\Bot\Api;
use GuzzleHttp\Client;
use Telegram\Bot\HttpClients\GuzzleHttpClient;

class TelegramPoll extends Command
{
    protected $signature = 'telegram:poll';
    protected $description = 'Poll Telegram for updates (Alternative to Webhook for Localhost)';

    public function handle(TelegramService $telegramService)
    {
        $this->info("=== TELEGRAM BOT POLLING MODE ===");
        
        $token = \App\Models\SystemSetting::where('key', 'telegram_bot_token')->first()->value;
        if (!$token || $token == 'YOUR_BOT_TOKEN') {
            $this->error("Error: Bot Token belum diatur di menu Settings atau .env!");
            return;
        }

        // Setup Telegram API with SSL ignore (same as TelegramService)
        $client = new Client(['verify' => false, 'timeout' => 30]);
        $httpClient = new GuzzleHttpClient($client);
        $telegram = new Api($token, false, $httpClient);
        
        try {
            $this->warn("Menonaktifkan Webhook agar Polling bisa berjalan...");
            $telegram->removeWebhook();
            $this->info("Berhasil! Menunggu pesan masuk...");
        } catch (\Exception $e) {
            $this->error("Gagal menghapus webhook: " . $e->getMessage());
        }

        $offset = 0;

        while (true) {
            try {
                $updates = $telegram->getUpdates([
                    'offset' => $offset,
                    'timeout' => 20
                ]);

                foreach ($updates as $update) {
                    $updateId = $update->getUpdateId();
                    $this->info("[" . date('H:i:s') . "] Menerima Update ID: $updateId");
                    
                    // Convert update object to array for handleUpdate
                    $telegramService->handleUpdate(json_decode($update->toJson(), true));
                    
                    $offset = $updateId + 1;
                }
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'cURL error 28')) {
                    // Timeout is normal for long polling
                    continue;
                }
                $this->error("Error: " . $e->getMessage());
                sleep(2);
            }
        }
    }
}
