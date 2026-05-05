<?php

namespace App\Http\Controllers;

use App\Models\NetworkDevice;
use Illuminate\Http\Request;

class NetworkDeviceController extends Controller
{
    public function index()
    {
        $devices = NetworkDevice::all();
        return view('devices.index', compact('devices'));
    }

    public function show(NetworkDevice $device, \App\Services\MikroTikService $mikrotik)
    {
        $stats = [];
        if ($device->type === 'mikrotik') {
            $stats = $mikrotik->getSystemResource();
        } else {
            // For other devices, we just show ping latency as real-time info
            $isOnline = $mikrotik->ping($device->ip_address);
            $stats = [
                'status' => $isOnline ? 'ONLINE' : 'OFFLINE',
                'last_ping' => now()->toTimeString(),
            ];
        }

        return view('devices.show', compact('device', 'stats'));
    }
}
