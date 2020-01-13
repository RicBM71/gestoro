<?php

namespace App\Http\Requests\Ventas;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlbaranRequest extends FormRequest
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
        $data= [
            'cliente_id'        => ['required','integer','min:1'],
            'tipo_id'           => ['required','integer'],
            'fecha_albaran'     => ['required','date'],
            'iva_no_residente'  => ['required', 'boolean'],
        ];

        return $data;
    }
}
