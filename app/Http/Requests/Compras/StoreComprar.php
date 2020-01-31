<?php

namespace App\Http\Requests\Compras;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Compras\LimiteEfectivoDepositoRule;

class StoreComprar extends FormRequest
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
            'concepto_id' => ['required','integer','between:13,15'],
            'cliente_id' => ['required','integer'],
            'compra_id'  => ['required','integer'],
            'fecha'      => ['required','date'],
            'importe'    => ['required', 'numeric', new LimiteEfectivoDepositoRule($this->cliente_id, $this->fecha, $this->compra_id, $this->concepto_id)],

        ];
    }

    public function messages() {
        return [
            'concepto_id.between' => 'El concepto proporcionado no es válido',
        ];
    }
}
