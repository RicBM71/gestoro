<?php

namespace App\Http\Requests\Ventas;

use App\Rules\PrecioCosteRule;
use App\Rules\Albaranes\UnidadesRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAlbalinRequest extends FormRequest
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
            'albaran_id'    => ['required', 'integer'],
            'producto_id'   => ['required', 'integer'],
            'unidades'      => ['required', 'numeric', new UnidadesRule($this->producto_id)],
            'precio_coste'  => ['required', 'numeric'],
            'importe_unidad'=> ['required', 'numeric', new PrecioCosteRule($this->precio_coste)],
            'iva_id'        => ['required', 'integer'],
            'iva'           => ['required', 'numeric'],
            'notas'         => ['nullable', 'string',  'max:190'],
        ];
    }
}
