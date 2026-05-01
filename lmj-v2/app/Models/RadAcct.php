<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadAcct extends Model
{
    protected $connection = 'radius';
    protected $table = 'radacct';
    public $timestamps = false;
    protected $primaryKey = 'radacctid';

    protected $guarded = [];
}
