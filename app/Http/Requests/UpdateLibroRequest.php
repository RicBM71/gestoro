<?php

namespace App\Http\Requests;

use App\Rules\LibroUniqueRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLibroRequest extends FormRequest
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
            'nombre'        => ['required', 'string', 'max:50'],
            'grupo_id'      => ['required', new LibroUniqueRule($this->ejercicio, $this->id)],
            'ejercicio'     => ['required','integer'],
            'ult_compra'    => ['required','integer'],
            'ult_factura'   => ['required','integer'],
            'serie_fac'     => ['required'],
            'serie_com'     => ['required'],
            'grabaciones'   => ['required'],
            'recompras'     => ['required'],
            'peso_frm'      => ['required'],
            'cerrado'       => ['required'],
            'semdia_bloqueo'=> ['required','string','max:3'],
            'dias_custodia' => ['required','integer',],
            'dias_cortesia' => ['required','integer',],
            'interes'       => ['required', 'numeric'],
            'interes_recuperacion'=> ['required', 'numeric'],
            'codigo_pol'    => ['string','nullable'],
            'nombre_csv'    => ['string','nullable'],
            'plantilla_excel'   => ['string','nullable', 'max:30'],
            'establecimiento'   => ['string','nullable', 'max:50'],
            'tramo'             => ['required', 'numeric'],
            'interes_min'       => ['required', 'numeric'],

        ];
    }
}
