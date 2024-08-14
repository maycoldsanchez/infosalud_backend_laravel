<?php

namespace App\AO;

use App\Models\Users\TokenUsers;
use Faker\Provider\Uuid;

class TokenUsersAO
{

    public static function setToken($user, $token, $ip)
    {
        $objToken = [
            'user_ip'   => $ip,
            'user_id'   => $user->id,
            'token'     => $token,
            'state'     => 1
        ];

        return TokenUsers::create($objToken)->get();
    }

    public static function disableAllTokens($user)
    {

        $objToken = [
           'state' => 0
        ];
        TokenUsers::where('user_id', $user->id)
            ->update($objToken);
    }

    public static function countLoggedRegister($infoToken)
    {
        return TokenUsers::where('user_ip', $infoToken['user_ip'])
            ->where('user_id', $infoToken['user'])
            ->where('token', $infoToken['token'])
            ->where('state', 1)
            ->count();
    }

    public static function disableToken($token)
    {

        $objToken['state'] = 0;
        TokenUsers::where('token', $token)
            ->update($objToken);
    }
}
