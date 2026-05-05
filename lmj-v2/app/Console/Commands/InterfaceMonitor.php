<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alert;
use App\Services\MikroTikService;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Cache;

class InterfaceMonitor extends Command
{
    protected $signature = 'mikrotik:monitor-interfaces {--loop}';
    protected $description = 'Monitor MikroTik ports for Up/Down status and send notifications';

    public function handle(MikroTikService $mikrotik, TelegramService $telegram)
    {
        $this->info('Monitoring Interface dimulai...');
        
        $loop = $this->option('loop');

        do {
            $interfaces = $mikrotik->getInterfaces();
            
            if (empty($interfaces)) {
                $this->error('Gagal mengambil data interface. Cek koneksi MikroTik.');
                if ($loop) {
                    sleep(5);
                    continue;
                }
                return;
            }

            foreach ($interfaces as $iface) {
                $name = $iface['name'];
                $status = $iface['running'] === 'true' ? 'UP' : 'DOWN';
                
                $cacheKey = "iface_status_{$name}";
                $oldStatus = Cache::get($cacheKey);

                if ($oldStatus !== null && $oldStatus !== $status) {
                    $emoji = $status === 'UP' ? '✅' : '🚨';
                    $title = "Port {$name} is " . ($status === 'UP' ? 'Connected' : 'Disconnected');
                    $message = "{$emoji} Kabel pada port {$name} baru saja " . ($status === 'UP' ? 'Dicolok/Aktif' : 'Dicabut/Mati') . ".";

                    \App\Models\Alert::create([
                        'title' => $title,
                        'level' => $status === 'UP' ? 'info' : 'critical',
                        'message' => $message,
                    ]);

                    $this->info("ALERT: {$title}");
                }

                Cache::forever($cacheKey, $status);
            }

            if ($loop) {
                usleep(2000000); // Tunggu 2 detik
            }
        } while ($loop);

        $this->info('Monitoring interface selesai.');
    }
}
