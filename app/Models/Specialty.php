<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    protected $table = 'specialty';
    protected $primaryKey = 'spec_code';
    public $timestamps = false;

    protected $fillable = [
        "spec_code",
        "spec_name"
    ];
}
