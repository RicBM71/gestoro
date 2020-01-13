<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContador extends FormRequest
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
            'tipo_id'   => ['required', 'integer'],
            'serie_albaran'=> ['required', 'max:1',Rule::unique('contadores')->where(function ($query) {
                                                        return $query->where('empresa_id',  session('empresa_id'))
                                                                      ->where('ejercicio', $this->ejercicio);})],
            'ejercicio' => ['required','integer', Rule::unique('contadores')->where(function ($query) {
                                return $query->where('empresa_id',  session('empresa_id'))
                                             ->where('tipo_id',  $this->tipo_id)
                                             ->where('ejercicio', $this->ejercicio);})],
            'ult_albaran'       => ['required', 'integer'],
            'ult_factura'       => ['required', 'integer'],
            'ult_factura_auto'  => ['required', 'integer'],
            'ult_factura_abono' => ['required', 'integer'],
        ];
    }
}
