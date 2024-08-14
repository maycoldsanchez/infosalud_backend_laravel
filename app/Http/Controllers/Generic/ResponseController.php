<?php

namespace App\Http\Controllers\Generic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ResponseController extends Controller
{
    public static function objectResponse($res) {
        $response = array();
        if ($res['status'] === 500 || $res['status'] === 423) {
            $response = array(
                'data'   => null,
                'message'    => $res['message'],
                'status' => $res['status']
            );
        } else if ($res['status'] === 422) {
            $response = array(
                'data'   => $res['data'],
                'message'    => $res['message'],
                'status' => $res['status']
            );
        } else {
            $response = array(
                'data'   => $res['data'],
                'message'    => 'Exito',
                'status' => 200
            );
        }

        return $response;
    }

    public static function exceptionResponse() {
        return array('message' => 'Error al consultar en la Base de Datos', 'status' => 500);
    }

    public static function purifyResponse($type) {
        return ($type == 'inyection')
            ? ['msm' => 'Se detecto un intento de inyeccion SQL', 'status' => 423]
            : ['msm' => 'Error al validar los datos, por favor vuelva a intertarlo nuevamente', 'status' => 500];
    }
}
