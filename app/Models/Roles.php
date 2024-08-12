<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users\User;

class Roles extends Model
{
    protected $fillable = [
        'name'
    ];

    protected $table = 'role';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    use HasFactory;

    public function users()
    {
        return $this->hasMany(User::class, "role", "role");
    }
}
