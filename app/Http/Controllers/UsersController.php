<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BL\Users\UsersBL;
use App\Http\Requests\Users\DeleteUsersRequest;
use App\Http\Requests\Users\UsersRequest;
use App\Http\Requests\Users\UsersEditRequest;
use App\Http\Requests\Users\getUsersRequest;
use App\Http\Requests\Users\getWorkCertificateByUserRequest;

class UsersController extends Controller
{
    public static function storeUser(UsersRequest $request){
        $response = UsersBL::create($request);
        return response()->json($response, 200);
    }

    public static function updateUser(UsersEditRequest $request){
        $response = UsersBL::update($request);
        return response()->json($response, 200);
    }

    public static function deleteUser(DeleteUsersRequest $request){
        $response = UsersBL::delete($request);
        return response()->json($response, 200);
    }

    public static function getUser(getUsersRequest $request){
        $response = UsersBL::getAllUsers($request);
        return response()->json($response, 200);
    }

    public static function import(Request $request){
        $response = UsersBL::import($request);
        return response()->json($response, 200);
    }

    public function validateUser($data, $document){
        return UsersBL::validateUser($data, $document);
    }

    public function getUserByDocumentLogin($data) {
        return UsersBL::getUserByDocumentLogin($data);
    }

    public function getTypeDocument(){
        $response = UsersBL::getTypeDocument();
        return response()->json($response, 200);
    }

    public function findUserByDocumentNumber(getWorkCertificateByUserRequest $request){
        $response = UsersBL::findUserByDocumentNumber($request);
        return response()->json($response, 200);
    }

    public function getUsersByCompany(){
        $response = UsersBL::getUsersByCompany();
        return response()->json($response, 200);
    }
}
