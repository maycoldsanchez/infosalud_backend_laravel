<?php

namespace App\BL;

use App\AO\UsersAO;
use App\AO\Roles\RolesAO;
use App\Http\Controllers\Generic\ResponseController;
use Illuminate\Support\Facades\Log;
use Faker\Provider\Uuid;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helpers\PurifyRequest\PurifyRequestHelper;
use Illuminate\Support\Facades\Hash;

class UsersBL{
    private $response = array();

    public function __construct(
        private ResponseController $responseController,
        private PurifyRequestHelper $puryfyTrair,
        private UsersAO $usersAO
    ) {}

    public function create($objUser)
    {
        try {
            $dataUser = $objUser->all();

            $validationPurify = $this->puryfyTrair->voidPurifyRequest($dataUser);
            if($validationPurify['valid']){
                $dataUser->password = Hash::make($objUser->password);
                $this->usersAO->createUser($dataUser);
                $this->response = ['data' => true, 'status' => 200];
                Log::info("User added successfully -> function create() UsersBL");
            }else{
                $this->response = $this->responseController->purifyResponse($validationPurify['type']);
            }
        } catch (\Throwable $th) {
            $this->response = $this->responseController->exceptionResponse();
            Log::error($th->getMessage(). ' -> function create() UsersBL');
        }

        return  $this->responseController->objectResponse($this->response);
    }

    public function update($objUser){
        try {
            $data = $this->usersAO->getUser($objUser->id);

            if ($data->isEmpty()) {
                $this->response = ['data' => 'user_does_not_exist', 'status' => 200];
            } else {
                $validationPurify = $this->puryfyTrair->voidPurifyRequest($objUser);
                if($validationPurify['valid']){
                    $this->usersAO->updateUser($objUser->except(['id']), $objUser->id);
                    $this->response = ['data' => 'updated record', 'status' => 200];
                    Log::info("User updated successfully -> function update() UsersBL");
                }else{
                    $this->response = $this->responseController->purifyResponse($validationPurify['type']);
                }
            }

        } catch (\Throwable $th) {
            $this->response = $this->responseController->exceptionResponse();
            Log::error($th->getMessage(). ' -> function update() UsersBL');
        }
        return  $this->responseController->objectResponse($this->response);
    }

    public function delete($objUser){
        try {
            $objUserDelete = ['id' => $objUser->id, 'deleted' => 1];
            $validationPurify = $this->puryfyTrair->voidPurifyRequest($objUserDelete);
            if($validationPurify['valid']){
                $this->usersAO->updateUser($objUserDelete, $objUser->id_user);
                $this->response = ['data' => 'deleted record', 'status' => 200];
                Log::info("User deleted successfully -> function delete() UsersBL");
            }else{
                $this->response = $this->responseController->purifyResponse($validationPurify['type']);
            }
        } catch (\Throwable $th) {
            $this->response = $this->responseController->exceptionResponse();
            Log::error($th->getMessage(). ' -> function delete() UsersBL');
        }
        return $this->responseController->objectResponse($this->response);
    }

    public function getAllUsers() {
        try {
            $data = $this->usersAO->getAllUsers();
            $this->response = ['data' => $data, 'status' => 200];
        } catch (\Throwable $th) {
            $this->response = $this->responseController->exceptionResponse();
            Log::error($th->getMessage(). ' -> function getAllUsers() UsersBL');
        }

        return $this->responseController->objectResponse($this->response);
    }

    public function getAllUsersTable($request)
    {
        try {
            $userGet = [
                'searchValue' => $request->searchValue ? $request->searchValue : ""
            ];

            $validationPurify = $this->puryfyTrair->voidPurifyRequest($userGet);
            if ($validationPurify['valid']) {
                $data = $this->usersAO->getAllUsersTable($request);
                $this->response = ['data' => $data, 'status' => 200];
                Log::info("Users selected successfully -> function getAllUsers() UsersBL");
            } else {
                $this->response = $this->responseController->purifyResponse($validationPurify['type']);
            }
        } catch (\Throwable $th) {
            $this->response = $this->responseController->exceptionResponse();
            Log::error($th->getMessage() . ' -> function getAllUsers() UsersBL');
        }

        return $this->responseController->objectResponse($this->response);
    }

    public function validateUser($data) {
        try {
            $validationPurify = $this->puryfyTrair->voidPurifyRequest(['email' => $data]);
            if($validationPurify['valid']){
                $objData = $this->usersAO->validateUser($data);
                $this->response = ['data' => $objData, 'status' => 200];
                Log::info("Successful consultation -> function validateUser()");
            }else{
                $this->response = $this->responseController->purifyResponse($validationPurify['type']);
            }

        } catch (\Throwable $th) {
            $this->response = $this->responseController->exceptionResponse();
            Log::error($th->getMessage()." function validateUser()");
        }
        return  $this->responseController->objectResponse($this->response);
    }

}
