<?php

namespace App\AO;

use App\Models\Users\TokenUsers;

class TokenUsersAO
{

    public static function setToken($infoToken)
    {
        $objToken = [
            'user_ip'      => $infoToken['user_ip'],
            'user_id' => $infoToken['user'],
            'token'   => $infoToken['token'],
            'state' => 1
        ];

        return TokenUsers::create($objToken)->get('ID');
    }

    public static function disableAllTokens($infoToken)
    {
        $objToken['state'] = 0;
        TokenUsers::where('user_id', $infoToken['user'])
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
