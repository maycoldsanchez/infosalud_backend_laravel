<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userAuth = JWTAuth::user();

        return [
            'email'     => ['required','email','max:50','unique:users'],
            'name'      => ['required', 'regex:/^[a-zA-Z0-9.ñÑáéíóúÁÉÍÓÚ]*$/i', 'max:50'],
            'password'  => ['required','min:8', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'],
            'role'      => ['required', Rule::in(['admin', 'medico', 'paciente', 'facturacion']),],
        ];
    }

    public function messages()
    {
        return [
            'email.required'    => 'El correo es obligatorio',
            'email.email'       => 'El correo no tiene el formato válido',
            'email.max'         => 'El email es de máximo 50 caracteres',
            'email.unique'      => 'El correo electrónico ya existe',

            'name.required'     => 'El nombre del usuario es obligatorio',
            'name.regex'        => 'El nombre de usuario sólo debe ser texto',
            'name.max'          => 'El nombre de usuario es de mÃ¡ximo 50 caracteres',

            'password.required' => 'La contraseña es obligatorio',
            'password.min'      => 'La contraseña debe ser de minimo 8 caracteres',
            'password.regex'    => 'La contraseña no cumple con el formato válido',

            'role.required'     => 'El rol del usuario es obligatorio',
            'role.in'           => 'El rol no es valido',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 422,
            'errors' => $validator->errors()->all()
        ], 200));
    }
}
