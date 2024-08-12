<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliationType extends Model
{
    protected $table = 'affiliation_type';
    protected $primaryKey = 'affi_code';
    public $timestamps = false;

    protected $fillable = [
        'affi_code', 'affi_name'
    ];
}
