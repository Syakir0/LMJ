<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\NetworkDevice;

class IspSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Packages
        Package::create([
            'name' => 'Home-10Mbps',
            'speed_limit' => 10,
            'price' => 150000,
            'mikrotik_profile' => 'UP-10M'
        ]);

        Package::create([
            'name' => 'Home-20Mbps',
            'speed_limit' => 20,
            'price' => 250000,
            'mikrotik_profile' => 'UP-20M'
        ]);

        Package::create([
            'name' => 'Business-50Mbps',
            'speed_limit' => 50,
            'price' => 500000,
            'mikrotik_profile' => 'UP-50M'
        ]);

        // 2. Seed Network Devices
        NetworkDevice::create([
            'name' => 'MikroTik Core CCR',
            'ip_address' => '192.168.10.1',
            'type' => 'mikrotik',
            'is_online' => true
        ]);

        NetworkDevice::create([
            'name' => 'Tenda Customer A',
            'ip_address' => '10.10.10.100',
            'type' => 'tenda',
            'is_online' => false
        ]);

        // 3. Seed Customers
        $p1 = Package::first();
        \App\Models\Customer::create([
            'name' => 'Budi Santoso',
            'username' => 'budi_malacca',
            'password' => 'budi123',
            'package_id' => $p1->id,
            'status' => 'active'
        ]);

        \App\Models\Customer::create([
            'name' => 'Ani Wijaya',
            'username' => 'ani_speedy',
            'password' => 'ani123',
            'package_id' => $p1->id,
            'status' => 'active'
        ]);

        \App\Models\Customer::create([
            'name' => 'Eko Prasetyo',
            'username' => 'eko_net',
            'password' => 'eko123',
            'package_id' => $p1->id,
            'status' => 'suspended'
        ]);

        \App\Models\Customer::create([
            'name' => 'Siti Aminah',
            'username' => 'siti_wifi',
            'password' => 'siti123',
            'package_id' => $p1->id,
            'status' => 'active'
        ]);

        // 4. Seed Initial Alerts
        \App\Models\Alert::create([
            'title' => 'System Online',
            'message' => 'Dashboard LMJ-ISP CORE berhasil diinisialisasi.',
            'level' => 'info'
        ]);

        \App\Models\Alert::create([
            'title' => 'Radius Connection Success',
            'message' => 'Koneksi ke Remote RADIUS Database (192.168.10.2) stabil.',
            'level' => 'info'
        ]);

        \App\Models\Alert::create([
            'title' => 'High Latency Detected',
            'message' => 'Latensi tinggi terdeteksi pada uplink Ether1 MikroTik Core.',
            'level' => 'warning'
        ]);

        \App\Models\Alert::create([
            'title' => 'New Customer Registered',
            'message' => 'Pelanggan baru Siti Aminah telah aktif.',
            'level' => 'info'
        ]);
    }
}
