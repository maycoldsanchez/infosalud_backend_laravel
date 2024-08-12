<?php

namespace App\Helpers\TokenUser;
use App\AO\TokenUsersAO;

class JwtAuthHelper
{
    public function saveToken($infoToken){
        TokenUsersAO::disableAllTokens($infoToken);
        TokenUsersAO::setToken($infoToken);
    }

    public function checkToken($infoToken){
        $intCountLoggedRegister = TokenUsersAO::countLoggedRegister($infoToken);
        if($intCountLoggedRegister == 1){
            $register = true;
        } else {
            $register = false;
        }
        return $register;
    }
}
