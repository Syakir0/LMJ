<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NetworkDevice;
use App\Models\Alert;
use App\Services\MikroTikService;
use App\Services\TelegramService;

class CheckDevices extends Command
{
    protected $signature = 'device:check-all';
    protected $description = 'Monitor all network devices status via Ping';

    public function handle(MikroTikService $mt, TelegramService $tg)
    {
        $devices = NetworkDevice::all();

        foreach ($devices as $device) {
            $isOnline = $mt->ping($device->ip_address);
            $oldStatus = $device->is_online;

            $device->update([
                'is_online' => $isOnline,
                'last_seen' => $isOnline ? now() : $device->last_seen
            ]);

            // Jika status berubah dari online ke offline
            if ($oldStatus && !$isOnline) {
                $message = "🔴 *DEVICE DOWN*\nName: {$device->name}\nIP: {$device->ip_address}\nTime: " . now();
                
                Alert::create([
                    'device_id' => $device->id,
                    'title' => 'Device Down',
                    'message' => $message,
                    'severity' => 'critical'
                ]);

                $tg->sendToNoc($message);
                $this->error("Device {$device->name} is DOWN!");
            }

            // Jika status berubah dari offline ke online
            if (!$oldStatus && $isOnline) {
                $message = "🟢 *DEVICE UP*\nName: {$device->name}\nIP: {$device->ip_address}\nTime: " . now();
                
                Alert::create([
                    'device_id' => $device->id,
                    'title' => 'Device Up',
                    'message' => $message,
                    'severity' => 'info'
                ]);

                $tg->sendToNoc($message);
                $this->info("Device {$device->name} is UP!");
            }
        }
    }
}
