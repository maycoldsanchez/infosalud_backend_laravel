<?php

namespace App\BL;

use App\AO\UsersAO;
use App\AO\Roles\RolesAO;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Generic\ResponseController;
use App\Import\UsersImport;
use Illuminate\Support\Facades\Log;
use Faker\Provider\Uuid;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helpers\PurifyRequest\PurifyRequestHelper;

class UsersBL{
    private static $response = [];
    private static $exception = ['msm' => 'Error al consultar en la Base de Datos', 'status' => 500];

    public static function create($objUser){
        try {
            $userAuth = JWTAuth::user();
            $data = UsersAO::getUser($objUser->id_user);
            $rolDefault = RolesAO::getRoleByName('USUARIO');
            if($data->isNotEmpty()){
                self::$response = ['data' => 'exist_user', 'status' => 200];
            }else{
                $iduser = Uuid::uuid();
                $objUserInsert = [
                    'id' => $iduser,
                    'email' => $objUser->email,
                    'created_by' => $userAuth->id,
                    'updated_by' => $userAuth->id,
                    'user_name' => $objUser->user_name,
                    'role' => $rolDefault[0]->id_role,
                    'business' => $userAuth->business,
                    'document_number' => $objUser->document_number
                ];

                $puryfyTrair = new PurifyRequestHelper();
                $validationPurify = $puryfyTrair->voidPurifyRequest($objUserInsert);
                if($validationPurify['valid']){
                    UsersAO::createUser($objUserInsert);
                    self::$response = ['data' => true, 'status' => 200];
                    Log::info("User added successfully -> function create() UsersBL");
                }else{
                    if($validationPurify['type'] == 'inyection'){
                        self::$response = ['msm' => 'Se detecto un intento de inyeccion SQL', 'status' => 423];
                    } else{
                        self::$response = ['msm' => 'Error al validar los datos, por favor vuelva a intertarlo nuevamente', 'status' => 500];
                    }
                }        
            }
        } catch (\Throwable $th) {
            self::$response = self::$exception;
            Log::error($th->getMessage(). ' -> function create() UsersBL');
        }

        return ResponseController::objectResponse(self::$response);
    }

    public static function update($objUser){
        try {
            $data = UsersAO::getUser($objUser->id_user);

            if ($data->isEmpty()) {
                self::$response = ['data' => 'user_does_not_exist', 'status' => 200];
            } else {
                $userAuth = JWTAuth::user();
                $objUserUpdate = [
                    'email' => $objUser->email,
                    'updated_by' => $userAuth->id,
                    'user_name' => $objUser->user_name,
                    'document_number' => $objUser->document_number,
                    'business' => $userAuth->business
                ];

                $puryfyTrair = new PurifyRequestHelper();
                $validationPurify = $puryfyTrair->voidPurifyRequest($objUserUpdate);
                if($validationPurify['valid']){
                    UsersAO::updateUser($objUserUpdate, $objUser->id_user);
                    self::$response = ['data' => 'updated record', 'status' => 200];
                    Log::info("User updated successfully -> function update() UsersBL");
                }else{
                    if($validationPurify['type'] == 'inyection'){
                        self::$response = ['msm' => 'Se detecto un intento de inyeccion SQL', 'status' => 423];
                    } else{
                        self::$response = ['msm' => 'Error al validar los datos, por favor vuelva a intertarlo nuevamente', 'status' => 500];
                    }
                }       
            }

        } catch (\Throwable $th) {
            self::$response = self::$exception;
            Log::error($th->getMessage(). ' -> function update() UsersBL');
        }
        return ResponseController::objectResponse(self::$response);
    }

    public static function delete($objUser){
        try {
            $objUserDelete = [
                'id' => $objUser->id_user,
                'deleted' => 1,
            ];

            $puryfyTrair = new PurifyRequestHelper();
            $validationPurify = $puryfyTrair->voidPurifyRequest($objUserDelete);
            if($validationPurify['valid']){
                UsersAO::updateUser($objUserDelete, $objUser->id_user);
                self::$response = ['data' => 'deleted record', 'status' => 200];
                Log::info("User deleted successfully -> function delete() UsersBL");
            }else{
                if($validationPurify['type'] == 'inyection'){
                    self::$response = ['msm' => 'Se detecto un intento de inyeccion SQL', 'status' => 423];
                } else{
                    self::$response = ['msm' => 'Error al validar los datos, por favor vuelva a intertarlo nuevamente', 'status' => 500];
                }
            }     
        } catch (\Throwable $th) {
            self::$response = self::$exception;
            Log::error($th->getMessage(). ' -> function delete() UsersBL');
        }
        return ResponseController::objectResponse(self::$response);
    }

    public static function getTypeDocument(){
        try {
            $data = UsersAO::getTypeDocument();
            self::$response = ['data' => $data, 'status' => 200];
            Log::info("Users selected successfully -> function getTypeDocument() UsersBL");
        } catch (\Throwable $th) {
            self::$response = self::$exception;
            Log::error($th->getMessage(). ' -> function getTypeDocument() UsersBL');
        }
        return ResponseController::objectResponse(self::$response);
    }

    public static function getAllUsers($request) {
        try {
            $userGet = [
                'searchValue' => $request['searchValue'] ? $request['searchValue'] : ""
            ];
            $puryfyTrair = new PurifyRequestHelper();
            $validationPurify = $puryfyTrair->voidPurifyRequest($userGet);
            if($validationPurify['valid']){
                $data = UsersAO::getAllUsers($request);
                self::$response = ['data' => $data, 'status' => 200];
                Log::info("Users selected successfully -> function getAllUsers() UsersBL");
            }else{
                if($validationPurify['type'] == 'inyection'){
                    self::$response = ['msm' => 'Se detecto un intento de inyeccion SQL', 'status' => 423];
                } else{
                    self::$response = ['msm' => 'Error al validar los datos, por favor vuelva a intertarlo nuevamente', 'status' => 500];
                }
            }     
            
        } catch (\Throwable $th) {
            self::$response = self::$exception;
            Log::error($th->getMessage(). ' -> function getAllUsers() UsersBL');
        }
        return ResponseController::objectResponse(self::$response);
    }

    public static function findUserByDocumentNumber($request) {
        try {
            $puryfyTrair = new PurifyRequestHelper();
            $validationPurify = $puryfyTrair->voidPurifyRequest(['data', $request['DOCUMENT_NUMBER']]);
            if($validationPurify['valid']){
                $data = UsersAO::getUserByDocument($request['DOCUMENT_NUMBER']);
                self::$response = ['data' => $data, 'status' => 200];
                Log::info("User selected successfully -> function findUserByDocumentNumber() UsersBL");
            }else{
                if($validationPurify['type'] == 'inyection'){
                    self::$response = ['msm' => 'Se detecto un intento de inyeccion SQL', 'status' => 423];
                } else{
                    self::$response = ['msm' => 'Error al validar los datos, por favor vuelva a intertarlo nuevamente', 'status' => 500];
                }
            }     
        } catch (\Throwable $th) {
            self::$response = self::$exception;
            Log::error($th->getMessage(). ' -> function findUserByDocumentNumber() UsersBL');
        }
        return ResponseController::objectResponse(self::$response);
    }


    public static function import($fileData) {
        set_time_limit(0);

        try {
            DB::beginTransaction();
            $usersImport = new UsersImport();
            Excel::import($usersImport, $fileData->file('file'));
            self::$response = ['data' => $usersImport::$errors, 'status' => 200];
            if (count($usersImport::$errors) > 0) {
                DB::rollBack();
            } else {
                DB::commit();
            }
            Log::info("Record import -> function import()");
        } catch (\Throwable $th) {
            DB::rollBack();
            self::$response = self::$exception;
            Log::error($th->getMessage()." function import()");
        }
        return ResponseController::objectResponse(self::$response);
    }

    public static function validateUser($data, $document) {
        try {

            $puryfyTrair = new PurifyRequestHelper();
            $validationPurify = $puryfyTrair->voidPurifyRequest(['username' => $data, 'document' => $document]);
            if($validationPurify['valid']){
                $objData = UsersAO::validateUser($data, $document);
                self::$response = ['data' => $objData, 'status' => 200];
                Log::info("Successful consultation -> function validateUser()");
            }else{
                if($validationPurify['type'] == 'inyection'){
                    self::$response = ['msm' => 'Se detecto un intento de inyeccion SQL', 'status' => 423];
                } else{
                    self::$response = ['msm' => 'Error al validar los datos, por favor vuelva a intertarlo nuevamente', 'status' => 500];
                }
            }     
            
        } catch (\Throwable $th) {
            self::$response = self::$exception;
            Log::error($th->getMessage()." function validateUser()");
        }
        return ResponseController::objectResponse(self::$response);
    }

    public static function getUserByDocumentLogin($data) {
        try {
            $puryfyTrair = new PurifyRequestHelper();
            $validationPurify = $puryfyTrair->voidPurifyRequest(['data' => $data,]);
            if($validationPurify['valid']){
                $objData = UsersAO::getUserByDocumentLogin($data);
                self::$response = ['data' => $objData, 'status' => 200];
                Log::info("Successful consultation -> function getUserByDocumentLogin()");
            }else{
                if($validationPurify['type'] == 'inyection'){
                    self::$response = ['msm' => 'Se detecto un intento de inyeccion SQL', 'status' => 423];
                } else{
                    self::$response = ['msm' => 'Error al validar los datos, por favor vuelva a intertarlo nuevamente', 'status' => 500];
                }
            }     
        } catch (\Throwable $th) {
            self::$response = self::$exception;
            Log::error($th->getMessage()." function getUserByDocumentLogin()");
        }
        return ResponseController::objectResponse(self::$response);
    }

    public static function getUsersByCompany(){
        try {
            $objData = UsersAO::getUsersByCompany();
            self::$response = ['data' => $objData, 'status' => 200];
            Log::info("Successful consultation -> function getUsersByCompany()");
        } catch (\Throwable $th) {
            self::$response = self::$exception;
            Log::error($th->getMessage()." function getUsersByCompany()");
        }
        return ResponseController::objectResponse(self::$response);
    }

}
