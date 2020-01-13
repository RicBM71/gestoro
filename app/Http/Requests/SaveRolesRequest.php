<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveRolesRequest extends FormRequest
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

        $rules = [
            'name'  => 'required',
            'guard_name'  => 'required',
        ];

        if ($this->method === 'POST'){
            $rules['name']  = 'required|unique:roles';
        }

        return $rules;
    }

    /**
    *   Clase 72, habla también de configurar en trans.php (es), ok!
    *
    */
    public function messages()
    {

        return [
            'name.required' => 'El nombre es requerido',
            'name.unique' => 'El nombre debe ser único'
        ];
    }
}
