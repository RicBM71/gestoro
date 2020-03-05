<?php

namespace App\Http\Requests\Compras;

use App\Rules\Compras\LimiteEfectivoDepositoRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeposito extends FormRequest
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
        $data = [

            'compra_id'     => ['required', 'integer'],
            'cliente_id'     => ['required', 'integer'],
            'importe'       => ['required', 'numeric', new LimiteEfectivoDepositoRule($this->cliente_id, $this->fecha, $this->compra_id, $this->concepto_id)],
            'concepto_id' => ['required','integer','between:1,3'],
            'fecha'         => ['nullable','date'],
            'iban'          => ['nullable','iban', 'max:50'],
            'bic'           => ['nullable','bic', 'max:11'],
            'notas'         => ['nullable', 'max:190']
        ];

        return $data;
    }

    public function messages() {
        return [
            'concepto_id.between' => 'El concepto proporcionado no es válido',
        ];
    }
}
