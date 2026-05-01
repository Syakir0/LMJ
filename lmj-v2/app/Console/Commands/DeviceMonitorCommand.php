<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NetworkDevice;
use App\Models\Alert;
use App\Services\MikroTikService;
use App\Services\TelegramService;

class DeviceMonitorCommand extends Command
{
    protected $signature = 'device:monitor';
    protected $description = 'Monitor all network devices and send alerts if offline';

    public function handle(MikroTikService $mikrotik, TelegramService $telegram)
    {
        $devices = NetworkDevice::all();

        foreach ($devices as $device) {
            $isOnline = $mikrotik->ping($device->ip_address);
            
            // Check for status change
            if ($device->is_online && !$isOnline) {
                // Device went OFFLINE
                $this->createAlert($device, 'critical', "Perangkat Down!", "Perangkat {$device->name} ({$device->ip_address}) tidak merespon PING.");
                $telegram->sendToNoc("🚨 *DEVICE DOWN*\nName: {$device->name}\nIP: {$device->ip_address}\nStatus: OFFLINE");
            } elseif (!$device->is_online && $isOnline) {
                // Device back ONLINE
                $this->createAlert($device, 'info', "Perangkat UP", "Perangkat {$device->name} ({$device->ip_address}) sudah kembali online.");
                $telegram->sendToNoc("✅ *DEVICE RECOVERED*\nName: {$device->name}\nIP: {$device->ip_address}\nStatus: ONLINE");
            }

            $device->update([
                'is_online' => $isOnline,
                'last_seen' => $isOnline ? now() : $device->last_seen
            ]);
        }

        $this->info('Monitoring selesai.');
    }

    private function createAlert($device, $level, $title, $message)
    {
        Alert::create([
            'device_id' => $device->id,
            'level' => $level,
            'title' => $title,
            'message' => $message,
        ]);
    }
}
