<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cie10 extends Model
{

    protected $table = 'cie10';
    protected $keyType = 'string';
    protected $primaryKey = 'cie_code';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        "cie_code", "cie_name", "cie_sex", "cie_limi", "cie_limf", "cie_mortality"
    ];
}
