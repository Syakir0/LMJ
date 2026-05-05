<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\RadCheck;

use App\Models\RadReply;
use App\Models\RadUserGroup;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        // 1. Auth (RadCheck)
        RadCheck::create([
            'username' => $customer->username,
            'attribute' => 'Cleartext-Password',
            'op' => ':=',
            'value' => $customer->password,
        ]);

        // 2. IP Address (RadReply)
        if ($customer->pppoe_ip) {
            RadReply::create([
                'username' => $customer->username,
                'attribute' => 'Framed-IP-Address',
                'op' => ':=',
                'value' => $customer->pppoe_ip,
            ]);
        }

        // 3. Package/Group (RadUserGroup)
        if ($customer->package) {
            RadUserGroup::create([
                'username' => $customer->username,
                'groupname' => $customer->package->name,
                'priority' => 1,
            ]);
        }
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        // 1. Update Auth
        if ($customer->isDirty('password')) {
            RadCheck::where('username', $customer->username)
                ->where('attribute', 'Cleartext-Password')
                ->update(['value' => $customer->password]);
        }

        // 2. Update IP
        if ($customer->isDirty('pppoe_ip')) {
            RadReply::updateOrCreate(
                ['username' => $customer->username, 'attribute' => 'Framed-IP-Address'],
                ['value' => $customer->pppoe_ip, 'op' => ':=']
            );
        }

        // 3. Update Package/Group
        if ($customer->isDirty('package_id')) {
            RadUserGroup::where('username', $customer->username)
                ->update(['groupname' => $customer->package->name]);
        }

        // Handle Username Change (Legacy sync)
        if ($customer->isDirty('username')) {
            $oldUsername = $customer->getOriginal('username');
            RadCheck::where('username', $oldUsername)->update(['username' => $customer->username]);
            RadReply::where('username', $oldUsername)->update(['username' => $customer->username]);
            RadUserGroup::where('username', $oldUsername)->update(['username' => $customer->username]);
        }
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        RadCheck::where('username', $customer->username)->delete();
        RadReply::where('username', $customer->username)->delete();
        RadUserGroup::where('username', $customer->username)->delete();
    }
}
