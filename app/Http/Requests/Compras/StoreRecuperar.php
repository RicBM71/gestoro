<?php

namespace App\Http\Requests\Compras;

use App\Compra;
use App\Rules\compras\AmpliacionAntesDeRecuperacion;
use App\Rules\Compras\FechaRecuperacion;
use App\Rules\Compras\ImporteMinRecuperacionRule;
use App\Rules\Compras\ImporteRecuperacion;
use App\Rules\Compras\LimiteEfectivoAcuenta;
use App\Rules\Compras\RetrasoRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecuperar extends FormRequest
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
        $compra = Compra::findOrFail($this->compra_id);

        return [
            'concepto_id' => ['required','integer','between:10,12'],
            'cliente_id' => ['required','integer'],
            'compra_id' => ['required','integer'],
            'fecha' => ['required','date', new FechaRecuperacion($compra), new RetrasoRule($compra), new AmpliacionAntesDeRecuperacion($compra)],
            'importe' => ['required','numeric', new ImporteRecuperacion($compra), new ImporteMinRecuperacionRule($compra), new LimiteEfectivoAcuenta($this->cliente_id, $this->fecha, $this->concepto_id)],
            'notas'         => ['nullable', 'max:190']
        ];
    }
}
