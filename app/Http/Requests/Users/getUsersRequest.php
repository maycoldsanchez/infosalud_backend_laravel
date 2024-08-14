<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class getUsersRequest extends FormRequest
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
            'searchValue'           => 'nullable|regex:/^[a-zA-Z0-9.ñÑáéíóúÁÉÍÓÚ]*$/i|max:50',
            'pageSize'              => 'nullable|regex:/^[0-9]+$/u|max:10',
            'page'                  => 'nullable|regex:/^[0-9]+$/u|max:10'
        ];
    }

    public function messages(){
        return [
            'searchValue.regex'     => 'El campo de búsqueda es de tipo numérico',
            'pageSize.regex'        => 'El campo tamaño de página es de tipo númerico',
            'page.regex'            => 'El campo página es de tipo numérico',

            'searchValue.max'       => 'El campo de búsqueda no puede superar 50 caracteres',
            'pageSize.max'          => 'La página no puede superar 15 caracteres',
            'page.max'              => 'El tamaño de página no puede superar 15 caracteres'
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
