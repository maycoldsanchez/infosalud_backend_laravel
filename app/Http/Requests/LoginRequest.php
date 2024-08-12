<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password'  => 'required',
            'email'     => ['required', 'email', 'max:50'],
        ];
    }

    public function messages(){
        return [
            'user.required'                   => 'El campo email es obligatorio.',
            'user.email'                      => 'El email no tiene el formato válido',
            'user.max'                        => 'El campo email no debe superar 50 caracteres.',
            
            'password.required'               => 'El campo contraseña es obligatorio.',
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
