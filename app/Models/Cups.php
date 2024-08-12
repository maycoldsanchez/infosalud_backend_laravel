<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cups extends Model
{
    protected $table = 'cups';
    protected $keyType = 'string';
    protected $primaryKey = 'cups_code';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        "cups_code",
        "cups_name",
        "cups_description",
        "cups_sex",
        "cups_years_start",
        "cups_years_end",
        "cups_iss",
        "cups_soat",
        "cups_particular",
        "cups_other",
        "cups_file",
        "cups_ot_type",
        "cups_state"
    ];
}
