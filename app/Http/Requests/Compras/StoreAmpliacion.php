<?php

namespace App\Http\Requests\Compras;

use App\Rules\Compras\FechaAmpliacion;
use Illuminate\Foundation\Http\FormRequest;

class StoreAmpliacion extends FormRequest
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

        $imp =  ($this->user()->hasPermissionTo('salefe') || $this->concepto_id >= 5) ?
                  ['required','numeric'] :
                  ['required','numeric','min:0','max:'.session('parametros')->lim_efe];

                  // lo hago en controlador porque necesito saber el retraso.
        // $dias = ($this->user()->hasRole('Supervisor') || $this->retraso) ?
        //             ['required','numeric'] :
        //             ['required','numeric','min:30'];

        $dias = ['required','numeric','min:0'];

        return [
            'concepto_id' => ['required','integer','between:4,6'],
            'cliente_id' => ['required','integer'],
            'compra_id' => ['required','integer'],
            'fecha' => ['required','date',new FechaAmpliacion($this->compra_id)],
            'importe' => $imp,
            'dias' => $dias,
            'notas' => ['string','nullable'],


        ];
    }

    public function messages() {
        return [
            'concepto_id.between' => 'El concepto proporcionado no es válido',
            'dias.min' => 'El valor es inferior a 30. (Sup)'
        ];
    }
}
