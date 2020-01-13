<?php

namespace App\Http\Requests\Compras;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompra extends FormRequest
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
            'grupo_id' => ['required','integer','min:1'],
            'dias_custodia' => ['required','integer'],
            'ejercicio' => ['required','integer'],
            'cliente_id' => ['required','integer','min:1'],
            'tipo_id' => ['required','integer'],
            'fecha_compra' => ['required','date'],
            'fecha_renovacion' => ['date','nullable'],
            'fecha_recogida' => ['date','nullable'],
            'importe' => ['required','numeric'],
            'importe_renovacion' => ['required','numeric'],
            'importe_acuenta' => ['required','numeric'],
            'interes' => ['required','numeric'],
            'fase_id' => ['required','integer'],
            'factura' => ['nullable','integer'],
            'fecha_factura' => ['date','nullable'],
            'serie_fac' => ['string','max:5','nullable'],
            'papeleta' => ['integer','nullable'],
            'notas' => ['string','nullable'],
            'username' => ['string','nullable'],

        ];

        if ($this->filled('albaran'))
            $data['albaran'] = ['integer', Rule::unique('compras')->where(function ($query) {
                return $query->where('grupo_id', $this->grupo_id)
                              ->where('ejercicio', $this->ejercicio);
            })];


        return $data;
    }
}
