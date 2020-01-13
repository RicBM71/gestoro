<?php

namespace App\Http\Requests\Compras;

use App\Clase;
use App\Rules\LeyesRule;
use App\Rules\PesoRule;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreComline extends FormRequest
{
    protected $clase;
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
        $this->clase = Clase::findOrFail($this->clase_id);

        return [
            'compra_id'      => ['required', 'integer'],
            'concepto'      => ['required', 'string'],
            'clase_id'      => ['required', 'integer'],
            'grabaciones'   => ['string','nullable'],
            'colores'       => ['string','nullable'],
            'importe'       => ['required', 'numeric'],
            'quilates'      => [Rule::requiredIf(function (){
                                                    return $this->clase->quilates;
                                                }),
                                            new LeyesRule($this->clase_id)],
            'peso_gr'       => [Rule::requiredIf(function (){
                                    return $this->clase->peso;
                                }), new PesoRule($this->clase->peso)],
        ];
    }


    public function messages()
    {
        return [
            'quilates.required' => 'Indicar quilates',
            'peso_gr.required'  => 'Indicar peso'
        ];
    }

}
