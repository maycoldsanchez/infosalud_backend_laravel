<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersEditRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'        => 'required|integer',
            'name'      => ['required', 'regex:/^[a-zA-Z0-9.ñÑáéíóúÁÉÍÓÚ]*$/i', 'max:50'],
            'role'      => ['required', Rule::in(['admin', 'medico', 'paciente', 'facturacion']),],
        ];
    }

    public function messages()
    {
        return [
            'id.required'       => 'El id del usurio es obligatorio.',
            'id.integer'     => 'El Id de usuario debe ser numérico',

            'name.required'     => 'El nombre del usuario es obligatorio',
            'name.regex'        => 'El nombre de usuario sólo debe ser texto',
            'name.max'          => 'El nombre de usuario es de mÃ¡ximo 50 caracteres',

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
