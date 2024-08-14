<?php

namespace App\BL;

use App\Helpers\TokenUser\JwtAuthHelper;
use App\Http\Controllers\Generic\ResponseController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthBL
{
    private $response = array();

    public function __construct(
        private JwtAuthHelper $jwtAuthHelper,
        private UsersController $usersController,
        private ResponseController $responseController
    ) {}

    public function login($request){
        try {
            $ipClient = $request->ip();
            $email     = $request->email;
            $checkUser = $this->checkUser($email, $ipClient);
            if ($checkUser['isCheck']) {
                $token = Auth::attempt($request->only('email', 'password'));
                if (!$token) {
                    $this->response = array(
                        'status' => 422,
                        'message' =>  'Credenciales incorrectas',
                    );
                } else {
                    $data = array(
                        'user' => $email,
                        'token' => $token,
                    );

                    $this->jwtAuthHelper->saveToken($checkUser['user'], $token, $ipClient);
                    $this->response = array(
                        'status' => 200,
                        'message' => 'Exito',
                        'data' => $data
                    );

                    activity()
                        ->withProperties(['ip' =>  $ipClient, 'user_email' => $email])
                        ->log('Autenticación exitosa');
                }

            } else {
                $this->response = $checkUser;
            }
        } catch (\Throwable $th) {
            dd($th);
            Log::error($th->getMessage() . ' -> function login() AuthController');
            $this->response = $this->responseController->exceptionResponse();
        }

        return $this->responseController->objectResponse($this->response);
    }

    public function checkUser($email, $ipClient){
        $validateUser = $this->usersController->validateUser($email);
        if (!empty($validateUser['data'])) {
            if ($validateUser['data']->deleted == 0) {
                $this->response = array(
                    'isCheck' => true,
                    'status' => 200,
                    'user' =>  $validateUser['data'],
                );
            } else {
                $this->response = array(
                    'isCheck' => false,
                    'status' => 500,
                    'message' =>  'Usuario deshabilitado en el sistema',
                );

                activity()
                    ->withProperties(['ip' =>  $ipClient, 'user_email' => $email])
                    ->log('Autenticación fallida');
            }
        } else {
            $this->response = array(
                'isCheck' => false,
                'status' => 422,
                'message' =>  'Credenciales incorrectas',
            );

            activity()
                ->withProperties(['ip' =>  $ipClient, 'user_email' => $email])
                ->log('Autenticación fallida');
        }

        return $this->response;
    }

    public function logout(){
        session()->forget('userIp');
        Auth::logout();
        return ['message' => 'Successfully logged out', 'status' => 200];
    }
}
