<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BL\UsersBL;
use App\Http\Requests\Users\DeleteUsersRequest;
use App\Http\Requests\Users\UsersRequest;
use App\Http\Requests\Users\UsersEditRequest;
use App\Http\Requests\Users\getUsersRequest;
use App\Http\Requests\Users\getWorkCertificateByUserRequest;

class UsersController extends Controller
{
    public function __construct(
        private UsersBL $userBl,
    ) {}

    public function storeUser(UsersRequest $request){
        $response = $this->userBl->create($request);
        return response()->json($response, 200);
    }

    public function updateUser(UsersEditRequest $request){
        $response = $this->userBl->update($request);
        return response()->json($response, 200);
    }

    public function deleteUser(DeleteUsersRequest $request){
        $response = $this->userBl->delete($request);
        return response()->json($response, 200);
    }

    public function getUsers()
    {
        $response = $this->userBl->getAllUsers();
        return response()->json($response, 200);
    }

    public function getUsersTable(getUsersRequest $request){
        $response = $this->userBl->getAllUsersTable($request);
        return response()->json($response, 200);
    }


    public function validateUser($data){
        return $this->userBl->validateUser($data);
    }
}
