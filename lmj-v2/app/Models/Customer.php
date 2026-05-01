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
        'telegram_id',
        'status',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
