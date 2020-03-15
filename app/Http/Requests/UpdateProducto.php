<?php

namespace App\Http\Requests;

use App\Clase;
use App\Rules\RebuRule;
use App\Rules\LeyesRule;
use App\Rules\UnivenRule;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProducto extends FormRequest
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

            'nombre'            => ['required', 'string', 'max:190'],
            'nombre_interno'    => ['nullable', 'string', 'max:190'],
            'clase_id'          => ['required','integer'],
            'estado_id'         => ['required','integer'],
            'compra_id'         => ['nullable','integer'],
            'iva_id'            => ['required','integer', new RebuRule($this->estado_id)],
            'referencia'        => ['required','max:20'],
            'univen'            => ['required', 'max:1'],
            'peso_gr'           => ['numeric','required'],
            'precio_coste'      => ['numeric','required'],
            'precio_venta'      => ['numeric','required'],
            'ref_pol'           => ['nullable', 'max:20', Rule::requiredIf($this->iva_id==2)],
            'caracteristicas'   => ['nullable','string', 'max:190'],
            'notas'             => ['string','nullable'],
            'cliente_id'        => ['nullable', 'integer', Rule::requiredIf($this->iva_id==2 && $this->empresa_id <> $this->destino_empresa_id && $this->compra_id == null)],
            'almacen_id'        => ['nullable', 'integer'],
            'etiqueta_id'       => ['required', 'integer'],
            'stock'             => ['required', 'integer'],
            'destino_empresa_id'=> ['required', 'integer'],
            'garantia_id'       => ['nullable', 'integer'],
            'online'            => ['boolean'],
            'fecha_ultima_revision' => ['nullable', 'date', Rule::requiredIf($this->garantia_id > 0)],
            'meses_garantia'    => ['nullable', 'integer','max:24', Rule::requiredIf($this->garantia_id > 0)],
        ];

        $clase = Clase::findOrFail($this->clase_id);

        if ($clase->quilates){
            $data['quilates']=['required', 'integer',new LeyesRule($this->clase_id)];
        }
        else
            $data['quilates']=['nullable', 'integer'];


        return $data;


    }

    public function messages()
    {

        return [
            'ref_pol.required' => 'La referencia policía es obligatoria',
            'cliente_id.required'=> 'Indicar un proveedor'
        ];
    }
}
