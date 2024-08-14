<?php

namespace App\Helpers\TokenUser;
use App\AO\TokenUsersAO;

class JwtAuthHelper
{

    public function __construct(
        private TokenUsersAO $tokenUsersAO
    ) {}

    public function saveToken($user, $token, $ip){
        $this->tokenUsersAO->disableAllTokens($user);
        $this->tokenUsersAO->setToken($user, $token, $ip);
    }

    public function checkToken($infoToken){
        $intCountLoggedRegister = $this->tokenUsersAO->countLoggedRegister($infoToken);
        if($intCountLoggedRegister == 1){
            $register = true;
        } else {
            $register = false;
        }
        return $register;
    }
}
