<?php

namespace App\AO;

use App\Models\Users\User;
use App\Models\AdditionalUserInformation\DocumentType;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersAO
{
    public static function createUser($objUser)
    {
        $user = User::create($objUser);
        return $user->id;
    }

    public static function updateUser($objUser, $userId)
    {
        $user = User::find($userId);
        return $user->update($objUser);
    }

    public static function getUser($userId)
    {
        return User::where('id', $userId)
        ->with(['userRole'])
        ->get();
    }

    public static function getAllUsers($request){
        $userAuth = JWTAuth::user();
        $result = User::with(
            [
                'userRole'
            ])
            ->active();
        if ($request['searchValue'] !== '') {
            $result = $result->where('email', 'LIKE', $request['searchValue'].'%')
                            ->paginate($request['pageSize']);
        } else {
            $result = $result->paginate($request['pageSize']);
        }
        return $result;
    }

    public static function validateUser($data, $document) {
        return User::select('id','deleted', 'role', 'email')
        ->where('email', $data)
        ->first();
    }
}
