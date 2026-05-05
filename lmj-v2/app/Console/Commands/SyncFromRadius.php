<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\RadCheck;
use App\Models\RadReply;
use App\Models\RadUserGroup;
use App\Models\Package;

class SyncFromRadius extends Command
{
    protected $signature = 'radius:sync-import';
    protected $description = 'Import existing users from Radius database into the Dashboard';

    public function handle()
    {
        $this->info('Starting sync from Radius...');

        $radUsers = RadCheck::where('attribute', 'Cleartext-Password')->get();

        foreach ($radUsers as $rad) {
            $username = $rad->username;
            $password = $rad->value;

            // Cari IP di RadReply
            $ip = RadReply::where('username', $username)
                ->where('attribute', 'Framed-IP-Address')
                ->value('value');

            // Cari Group di RadUserGroup
            $groupName = RadUserGroup::where('username', $username)->value('groupname');
            
            $package = null;
            if ($groupName) {
                $package = Package::where('name', $groupName)->first();
                if (!$package) {
                    $package = Package::create([
                        'name' => $groupName,
                        'speed_limit' => 10, // Default
                        'price' => 0,
                    ]);
                }
            }

            Customer::updateOrCreate(
                ['username' => $username],
                [
                    'name' => $username,
                    'password' => $password,
                    'pppoe_ip' => $ip,
                    'package_id' => $package ? $package->id : 1, // Fallback ke ID 1 jika tidak ada group
                    'status' => 'active',
                ]
            );

            $this->info("Synced: $username");
        }

        $this->info('Sync completed!');
    }
}
