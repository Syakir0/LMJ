<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetworkDevice extends Model
{
    protected $fillable = [
        'name',
        'username',
        'ip_address',
        'type',
        'is_online',
        'last_seen',
        'snmp_community',
    ];

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'device_id');
    }
}
