<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NetworkDevice;
use App\Models\Alert;
use App\Services\MikroTikService;
use App\Services\TelegramService;

class CheckDevices extends Command
{
    protected $signature = 'device:check-all {--loop}';
    protected $description = 'Monitor all network devices status via Ping & PPPoE fallback';

    public function handle(MikroTikService $mt, TelegramService $tg)
    {
        $this->info('Monitoring Perangkat dimulai...');
        $loop = $this->option('loop');

        do {
            $devices = NetworkDevice::all();
            $pppoeSessions = $mt->getActivePppoe();
            $activeIps = array_column($pppoeSessions, 'address');

            foreach ($devices as $device) {
                $isOnline = false;
                $newIp = $device->ip_address;

                // 1. Cek via Username PPPoE (Prioritas utama)
                if ($device->username) {
                    foreach ($pppoeSessions as $session) {
                        if (($session['user'] ?? $session['name'] ?? '') === $device->username) {
                            $isOnline = true;
                            $newIp = $session['address'] ?? $newIp;
                            break;
                        }
                    }
                }

                // 2. Cek via ICMP Ping (Jika belum online atau tidak ada username)
                if (!$isOnline) {
                    $isOnline = $mt->ping($device->ip_address);
                    
                    // 3. Cek via IP di PPPoE Active (Jika ping gagal)
                    if (!$isOnline && in_array($device->ip_address, $activeIps)) {
                        $isOnline = true;
                    }
                }

                $oldStatus = $device->is_online;

                if ($oldStatus != $isOnline || $device->ip_address != $newIp) {
                    $device->update([
                        'is_online' => $isOnline,
                        'ip_address' => $newIp,
                        'last_seen' => $isOnline ? now() : $device->last_seen
                    ]);

                    $message = $isOnline 
                        ? "🟢 *DEVICE UP*\nName: {$device->name}\nIP: {$device->ip_address}"
                        : "🔴 *DEVICE DOWN*\nName: {$device->name}\nIP: {$device->ip_address}";

                    \App\Models\Alert::create([
                        'device_id' => $device->id,
                        'title' => $isOnline ? 'Device Up' : 'Device Down',
                        'message' => $message,
                        'level' => $isOnline ? 'info' : 'critical'
                    ]);

                    $this->info($isOnline ? "UP: {$device->name}" : "DOWN: {$device->name}");
                }
            }

            if ($loop) {
                sleep(10); // Tunggu 10 detik
            }
        } while ($loop);
    }
}
