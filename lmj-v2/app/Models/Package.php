<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'speed_limit',
        'price',
        'mikrotik_profile',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
