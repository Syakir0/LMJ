<?php

namespace App\Http\Controllers;

use App\Services\MikroTikService;
use App\Models\NetworkDevice;
use App\Models\Customer;
use Illuminate\Http\Request;

class DiscoveryController extends Controller
{
    public function index(MikroTikService $mt)
    {
        $neighbors = $mt->getNeighbors();
        $leases = $mt->getDhcpLeases();
        $arp = $mt->getArpTable();
        
        $knownIps = NetworkDevice::pluck('ip_address')->toArray();
        $knownUsernames = Customer::pluck('username')->toArray();
        
        // Filter out known neighbors
        $newNeighbors = array_filter($neighbors, function($n) use ($knownIps) {
            return !in_array($n['address'] ?? '', $knownIps);
        });

        // Filter out known leases
        $newLeases = array_filter($leases, function($l) use ($knownIps) {
            return !in_array($l['address'] ?? '', $knownIps);
        });

        // Filter out known ARP entries (exclude gateway IPs and known devices)
        $newArp = array_filter($arp, function($a) use ($knownIps) {
            $ip = $a['address'] ?? '';
            // Skip common gateway IPs and known IPs
            return !in_array($ip, $knownIps) && !str_ends_with($ip, '.1');
        });

        return view('discovery.index', compact('newNeighbors', 'newLeases', 'newArp'));
    }

    public function addToDevices(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'ip_address' => 'required|ip',
            'type' => 'required|string'
        ]);

        NetworkDevice::create([
            'name' => $request->name,
            'ip_address' => $request->ip_address,
            'type' => $request->type,
            'is_online' => true,
            'last_seen' => now()
        ]);

        return redirect()->route('discovery.index')->with('success', 'Perangkat berhasil ditambahkan ke monitoring!');
    }
}
