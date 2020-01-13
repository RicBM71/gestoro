<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContador extends FormRequest
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
            'serie_albaran'=> ['required', 'max:3',Rule::unique('contadores')->ignore($this->id)->where(function ($query) {
                                                     return $query->where('empresa_id',  session('empresa_id'))
                                                    ->where('ejercicio', $this->ejercicio);})],

            'ejercicio' => ['required','integer', Rule::unique('contadores')->ignore($this->id)->where(function ($query) {
                                return $query->where('empresa_id',  session('empresa_id'))
                                              ->where('tipo_id',  $this->tipo_id)
                                              ->where('ejercicio', $this->ejercicio);})],
            'ult_albaran'         => ['required', 'integer'],
            'ult_factura'         => ['required', 'integer'],
            'serie_factura'       => ['required', 'max:3'],
            'ult_factura_auto'    => ['required', 'integer'],
            'serie_factura_auto'  => ['required', 'max:3'],
            'ult_factura_abono'   => ['required', 'integer'],
            'serie_factura_abono' => ['required', 'max:3'],
            'cerrado'             => ['required', 'boolean']

        ];
    }
}
