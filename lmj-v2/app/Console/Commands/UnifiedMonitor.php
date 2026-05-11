<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alert;
use App\Models\NetworkDevice;
use App\Services\MikroTikService;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class UnifiedMonitor extends Command
{
    protected $signature = 'system:monitor-all';
    protected $description = 'Unified monitoring for MikroTik interfaces, network devices, and automated billing';

    public function handle(MikroTikService $mt, TelegramService $tg)
    {
        $this->info('🚀 Unified Monitoring & Billing System Started...');
        $this->info('Press Ctrl+C to stop.');

        $lastBillingDate = Cache::get('last_billing_run_date');
        $lastOverdueCheck = Cache::get('last_overdue_check_time', 0);
        $lastTelegramPoll = 0;

        // Ensure we don't run overdue check immediately on restart if it was run recently
        // This prevents spamming on every restart.
        $timeSinceLastOverdue = time() - $lastOverdueCheck;
        if ($timeSinceLastOverdue < 3600 && $lastOverdueCheck > 0) {
            $this->info('Skipping initial overdue check, last run was ' . round($timeSinceLastOverdue/60) . ' mins ago.');
        } else {
            $lastOverdueCheck = 0; // Force run if no record or > 1hr
        }

        // Force remove webhook on startup to ensure polling works
        try {
            $token = \App\Models\SystemSetting::where('key', 'telegram_bot_token')->first()->value;
            if ($token && $token != 'YOUR_BOT_TOKEN') {
                $client = new \GuzzleHttp\Client(['verify' => false, 'timeout' => 5]);
                $httpClient = new \Telegram\Bot\HttpClients\GuzzleHttpClient($client);
                $telegram = new \Telegram\Bot\Api($token, false, $httpClient);
                $telegram->removeWebhook();
                $this->info('✅ Telegram Webhook removed. Polling mode active.');
            }
        } catch (\Exception $e) {
            $this->warn('Could not remove webhook: ' . $e->getMessage());
        }

        $tgOffset = 0;

        while (true) {
            try {
                $now = now();

                // 0. Telegram Polling (Every 5 seconds)
                if (time() - $lastTelegramPoll > 5) {
                    $token = \App\Models\SystemSetting::where('key', 'telegram_bot_token')->first()->value;
                    if ($token && $token != 'YOUR_BOT_TOKEN') {
                        try {
                            $client = new \GuzzleHttp\Client(['verify' => false, 'timeout' => 5]);
                            $httpClient = new \Telegram\Bot\HttpClients\GuzzleHttpClient($client);
                            $telegram = new \Telegram\Bot\Api($token, false, $httpClient);
                            
                            $updates = $telegram->getUpdates([
                                'offset' => $tgOffset,
                                'timeout' => 1 // Short timeout for unified loop
                            ]);

                            foreach ($updates as $update) {
                                $updateId = $update->getUpdateId();
                                $tg->handleUpdate(json_decode($update->toJson(), true));
                                $tgOffset = $updateId + 1;
                                $this->info("[Telegram] Update processed (ID: $updateId)");
                            }
                        } catch (\Exception $e) {
                            // Silently ignore telegram errors in unified loop unless they are critical
                            if (!str_contains($e->getMessage(), 'cURL error 28')) {
                                Log::debug('Telegram Poll Error: ' . $e->getMessage());
                            }
                        }
                    }
                    $lastTelegramPoll = time();
                }

                // 1. Periodic Billing Tasks
                // Generate Invoices once a day
                if ($lastBillingDate !== $now->toDateString()) {
                    $this->info('Running daily billing generation...');
                    Artisan::call('billing:generate');
                    $lastBillingDate = $now->toDateString();
                    Cache::forever('last_billing_run_date', $lastBillingDate);
                }

                // Check for overdue invoices every 1 hour
                if (time() - $lastOverdueCheck > 3600) {
                    $this->info('Running hourly overdue check...');
                    Artisan::call('billing:check-overdue');
                    $lastOverdueCheck = time();
                    Cache::forever('last_overdue_check_time', $lastOverdueCheck);
                }

                // 2. Real-time Monitoring Data
                $interfaces = $mt->getInterfaces();
                $pppoeSessions = $mt->getActivePppoe();
                $activeIps = array_column($pppoeSessions, 'address');
                $activeUsernames = [];
                foreach($pppoeSessions as $s) {
                    $activeUsernames[] = $s['user'] ?? $s['name'] ?? '';
                }

                // 2. Monitor Physical Interfaces (Port Up/Down)
                if (!empty($interfaces)) {
                    foreach ($interfaces as $iface) {
                        $name = $iface['name'];
                        $status = $iface['running'] === 'true' ? 'UP' : 'DOWN';
                        $cacheKey = "iface_status_{$name}";
                        $oldStatus = Cache::get($cacheKey);

                        if ($oldStatus !== null && $oldStatus !== $status) {
                            $emoji = $status === 'UP' ? '✅' : '🚨';
                            $title = "Port {$name} is " . ($status === 'UP' ? 'Connected' : 'Disconnected');
                            $msg = "{$emoji} Kabel port {$name} " . ($status === 'UP' ? 'Dicolok/Aktif' : 'Dicabut/Mati') . ".";
                            
                            Alert::create(['title' => $title, 'level' => $status === 'UP' ? 'info' : 'critical', 'message' => $msg]);
                            $tg->sendToNoc("*PORT STATUS CHANGE*\n" . $msg);
                            $this->warn("[$name] STATUS CHANGE: $status");
                        }
                        Cache::forever($cacheKey, $status);
                    }
                }

                // 3. Monitor Network Devices (Tenda, OLT, etc.)
                $devices = NetworkDevice::all();
                foreach ($devices as $device) {
                    $isOnline = false;
                    $newIp = $device->ip_address;

                    // Priority 1: Check by Username
                    if ($device->username && in_array($device->username, $activeUsernames)) {
                        $isOnline = true;
                        // Find dynamic IP from session
                        foreach($pppoeSessions as $s) {
                            if(($s['user'] ?? $s['name'] ?? '') === $device->username) {
                                $newIp = $s['address'] ?? $newIp;
                                break;
                            }
                        }
                    }

                    // Priority 2: ICMP Ping
                    if (!$isOnline) {
                        $isOnline = $mt->ping($device->ip_address);
                    }

                    // Priority 3: Fallback by IP in PPPoE Active
                    if (!$isOnline && in_array($device->ip_address, $activeIps)) {
                        $isOnline = true;
                    }

                    $oldStatus = $device->is_online;
                    if ($oldStatus != $isOnline || $device->ip_address != $newIp) {
                        $device->update(['is_online' => $isOnline, 'ip_address' => $newIp, 'last_seen' => $isOnline ? now() : $device->last_seen]);
                        
                        $statusText = $isOnline ? 'UP' : 'DOWN';
                        $emoji = $isOnline ? '🟢' : '🔴';
                        $title = "Device {$device->name} is $statusText";
                        $alertMsg = "Perangkat {$device->name} ($newIp) berstatus $statusText";
                        
                        Alert::create(['device_id' => $device->id, 'title' => $title, 'level' => $isOnline ? 'info' : 'critical', 'message' => $alertMsg]);
                        $tg->sendToNoc("{$emoji} *DEVICE STATUS CHANGE*\nName: {$device->name}\nIP: $newIp\nStatus: $statusText");
                        
                        $this->info("[$device->name] $statusText");
                    }
                }

            } catch (\Exception $e) {
                $this->error('Monitoring Error: ' . $e->getMessage());
                Log::error('UnifiedMonitor: ' . $e->getMessage());
                // Short sleep on error to prevent CPU spike
                sleep(5);
            }

            // Sleep for 3 seconds before next cycle
            usleep(3000000);
        }
    }
}
