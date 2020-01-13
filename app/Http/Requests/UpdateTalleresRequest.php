<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTalleresRequest extends FormRequest
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
        $data = [

            'nombre'            => ['required', 'string', 'max:30'],
            'apellidos'         => ['required', 'string', 'max:45'],
            'razon'             => ['nullable','string', 'max:70'],
            'direccion'         => ['nullable','string', 'max:50'],
            'cpostal'           => ['nullable','string', 'max:10'],
            'poblacion'         => ['nullable','string', 'max:40'],
            'provincia'         => ['nullable','string', 'max:30'],
            'telefono1'         => ['nullable','string', 'max:15'],
            'telefono2'         => ['nullable','string', 'max:15'],
            'tfmovil'           => ['nullable','string', 'max:15'],
            'email'             => ['nullable','email', 'max:50'],
            'tipodoc'           => ['string'],
            'dni'               => ['required', Rule::unique('talleres')->ignore($this->route('tallere')->id)],
            'notas'             => ['nullable','string'],
            'facturar'          => ['boolean'],
            'iban'              => ['nullable','iban', 'max:50'],
            'bic'               => ['nullable','bic', 'max:11'],
            'fpago_id'          => ['integer'],
        ];


        return $data;
    }
}
