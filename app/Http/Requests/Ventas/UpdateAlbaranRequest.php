<?php

namespace App\Http\Requests\Ventas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlbaranRequest extends FormRequest
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
            'fecha_albaran' => ['required', 'date'],
            'clitxt'        => ['nullable', 'string'],
            'notas_int'     => ['nullable', 'string'],
            'notas_ext'     => ['nullable', 'string'],
            'cuenta_id'     => ['nullable', 'integer'],
            'fpago_id'      => ['nullable','integer'],
            // 'taller_id'     => ['nullable','integer'],
            'facturar'      => ['required','boolean'],
            'express'       => ['required','boolean'],
            'fecha_notificacion' => ['nullable', 'date'],
        ];

        if ($this->tipo_id == 5){
            $data['taller_id']=['required','integer'];
            $data['procedencia_empresa_id']=['required','integer'];
        }

        return $data;
    }
}
