<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'username',
        'password',
        'pppoe_ip',
        'package_id',
        'billing_date',
        'due_date',
        'payment_status',
        'telegram_id',
        'phone',
        'telegram_chat_id',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function device()
    {
        return $this->hasOne(NetworkDevice::class, 'username', 'username');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
