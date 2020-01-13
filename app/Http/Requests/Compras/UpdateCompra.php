<?php

namespace App\Http\Requests\Compras;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompra extends FormRequest
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
        // if ($this->user()->hasPermissionTo('Edita Compras')){
            return [
                'dias_custodia'=> ['required', 'numeric'],
                'interes'      => ['required','numeric'],
                'papeleta'     => ['numeric','nullable'],
                'fecha_compra' => ['required','date'],
                'albaran'      => ['required','integer','min:1'],
                'notas'        => ['nullable','max:191'],
                'fase_id'      => ['integer','required'],
                'tipo_id'      => ['integer','required'],
            ];
        // }else{
        //     return [
        //         'fecha_compra'  => ['required','date'],
        //         'dias_custodia' => ['required', 'numeric'],
        //         'interes'       => ['required','numeric','min:1'],
        //         'papeleta' => ['integer','nullable'],
        //         'notas'         => ['nullable','max:100'],
        //         'fase_id'      => ['integer','required'],
        //         'tipo_id'      => ['integer','required'],
        //     ];

        // }
    }
}
