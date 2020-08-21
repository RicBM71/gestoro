<?php

namespace App\Http\Requests\Compras;

use App\Compra;
use App\Rules\Compras\RetrasoRule;
use App\Rules\Compras\FechaRecuperacion;
use App\Rules\Compras\ImporteRecuperacion;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Compras\ImporteMinRecuperacionRule;
use App\Rules\Compras\LimiteEfectivoRecuperarRule;
use App\Rules\Compras\AmpliacionAntesRecu;

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
            'concepto_id'   => ['required','integer','between:10,12'],
            'cliente_id'    => ['required','integer'],
            'compra_id'     => ['required','integer'],
            'fecha'         => ['required','date', new FechaRecuperacion($compra), new RetrasoRule($compra), new AmpliacionAntesRecu($compra)],
            'importe1'      => ['numeric',  new LimiteEfectivoRecuperarRule($this->fecha, $compra, $this->concepto_id, $this->importe)],
            'importe2'      => ['numeric', new ImporteRecuperacion($compra, $this->importe), new ImporteMinRecuperacionRule($compra, $this->importe)],
            'importe'       => ['required','numeric',new LimiteEfectivoRecuperarRule($this->fecha, $compra, $this->concepto_id, $this->importe),new ImporteRecuperacion($compra, $this->importe), new ImporteMinRecuperacionRule($compra, $this->importe)],
            'concepto_id2'  => ['integer','between:10,12'],
            'notas'         => ['nullable', 'max:190']
        ];
    }
}
