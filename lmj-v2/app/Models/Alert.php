<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'device_id',
        'customer_id',
        'title',
        'message',
        'level',
        'is_sent',
    ];

    public function device()
    {
        return $this->belongsTo(NetworkDevice::class, 'device_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
