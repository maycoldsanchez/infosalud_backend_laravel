<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{

    protected $table = 'cities';
    protected $keyType = 'string';
    protected $primaryKey = 'city_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'city_id', 'city_idapi', 'city_name', 'city_dpto'
    ];
}
