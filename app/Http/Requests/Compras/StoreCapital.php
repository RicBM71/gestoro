<?php

namespace App\Http\Requests\Compras;

use App\Compra;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Compras\LimiteEfectivoDepositoRule;

class StoreCapital extends FormRequest
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
            'concepto_id' => ['required','integer','between:16,18'],
            'cliente_id' => ['required','integer'],
            'compra_id' => ['required','integer'],
            'fecha' => ['required','date'],
            'importe'    => ['required', 'numeric', new LimiteEfectivoDepositoRule($this->cliente_id, $this->fecha, $this->compra_id, $this->concepto_id)],
            'notas'         => ['nullable', 'max:190']
        ];
    }

    public function messages() {
        return [
            'concepto_id.between' => 'El concepto proporcionado no es válido'
        ];
    }
}
