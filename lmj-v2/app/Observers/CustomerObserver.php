<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\RadCheck;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        // Tambahkan user ke RadCheck (Cleartext-Password)
        RadCheck::create([
            'username' => $customer->username,
            'attribute' => 'Cleartext-Password',
            'op' => ':=',
            'value' => $customer->password,
        ]);
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        if ($customer->isDirty('password')) {
            RadCheck::where('username', $customer->username)
                ->where('attribute', 'Cleartext-Password')
                ->update(['value' => $customer->password]);
        }

        if ($customer->isDirty('username')) {
            RadCheck::where('username', $customer->getOriginal('username'))
                ->update(['username' => $customer->username]);
        }
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        RadCheck::where('username', $customer->username)->delete();
    }
}
