<?php

namespace App\AO\Roles;

use App\Models\Roles;

class RolesAO
{
    public static function getAllRoles(){
        return Roles::all();
    }

    public static function getRoleByName($data) {
        return Roles::where('name', $data)->get();
    }
}
