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

    public function list()
    {
        return response()->json(NetworkDevice::all());
    }

    public function show(NetworkDevice $device, \App\Services\MikroTikService $mikrotik)
    {
        $stats = [];
        if ($device->type === 'mikrotik') {
            $stats = $mikrotik->getSystemResource();
        } else {
            // Check Ping
            $isOnline = $mikrotik->ping($device->ip_address);
            
            // Fallback: Check PPPoE Active Sessions if ping fails
            if (!$isOnline) {
                $pppoeSessions = $mikrotik->getActivePppoe();
                foreach ($pppoeSessions as $session) {
                    if (isset($session['address']) && $session['address'] === $device->ip_address) {
                        $isOnline = true;
                        break;
                    }
                }
            }

            $stats = [
                'status' => $isOnline ? 'ONLINE' : 'OFFLINE',
                'last_ping' => now()->toTimeString(),
            ];
        }

        return view('devices.show', compact('device', 'stats'));
    }
}
