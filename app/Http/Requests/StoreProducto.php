<?php

namespace App\Http\Requests;

use App\Clase;
use App\Rules\PesoRule;
use App\Rules\LeyesRule;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProducto extends FormRequest
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

        $data = [

            'nombre'           => ['required', 'string', 'max:190'],
            'clase_id'         => ['required','integer'],
            'estado_id'        => ['required','integer'],
            'compra_id'        => ['nullable','integer'],
            'iva_id'           => ['required','integer'],
            'quilates'         => ['nullable','integer',new LeyesRule($this->clase_id)],
            'caracteristicas'  => ['nullable','string','max:190'],
            'peso_gr'          => ['numeric', new PesoRule($this->clase->peso)],
            'precio_coste'     => ['numeric','required'],
            'precio_venta'     => ['numeric','required'],
            'ref_pol'          => ['nullable','string', 'max:20'],
            'cliente_id'       => ['nullable','integer'],
            'univen'           => ['required', 'string', 'max:1'],
            'etiqueta_id'       => ['nullable','integer'],

        ];

        return $data;
    }
}
