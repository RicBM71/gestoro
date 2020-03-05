<?php

namespace App\Http\Requests\Compras;

use App\Compra;
use App\Rules\Compras\ImporteAcuenta;
use App\Rules\Compras\LimiteEfectivoAcuenta;
use Illuminate\Foundation\Http\FormRequest;

class StoreAcuenta extends FormRequest
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

        $imp = ($this->user()->hasRole('Supervisor')) ?
                    ['required','numeric',new ImporteAcuenta($compra), new LimiteEfectivoAcuenta($this->cliente_id, $this->fecha, $this->concepto_id)] :
                    ['required','numeric','min:0',new ImporteAcuenta($compra), new LimiteEfectivoAcuenta($this->cliente_id, $this->fecha, $this->concepto_id)];

        return [
            'concepto_id' => ['required','integer','between:7,9'],
            'cliente_id' => ['required','integer'],
            'compra_id' => ['required','integer'],
            'fecha' => ['required','date'],
            'importe' => $imp,
            'notas'         => ['nullable', 'max:190']
        ];
    }

    public function messages() {
        return [
            'concepto_id.between' => 'El concepto proporcionado no es válido',
            'importe.min' => 'El importe no puede ser inferior a 0. (Sup)'
        ];
    }
}
