<?php

namespace App\Http\Controllers\Auth;

use App\BL\AuthBL;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{

    public function __construct(
        private AuthBL $authBL
    ) {}


    public function login(LoginRequest $request) {
        $response = $this->authBL->login($request);
        return response()->json($response, 200);
    }

    public function me(){
        return JWTAuth::parseToken()->authenticate()->load('userRole');
    }

    public function logout()
    {
        $response = $this->authBL->logout();
        return response()->json($response, 200);
    }

}
