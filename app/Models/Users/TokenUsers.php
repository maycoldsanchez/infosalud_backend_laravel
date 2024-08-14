<?php

namespace App\Models\Users;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TokenUsers extends Model
{
    use LogsActivity;
    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected $fillable = ['id', 'user_ip', 'user_id', 'token', 'state'];
    protected $table = 'token_users';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_ip', 'user_id', 'token', 'state'])
            ->useLogName('TokenUsers')
            ->logOnlyDirty();
    }

    protected static function booted(): void
    {
        static::creating(function (TokenUsers $tokenUsers) {
            $tokenUsers->id = Str::uuid();
        });
    }
}
