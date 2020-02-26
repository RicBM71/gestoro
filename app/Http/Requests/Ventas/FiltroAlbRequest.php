<?php

namespace App\Http\Requests\Ventas;

use App\Rules\RangoFechaRule;
use App\Rules\MaxDiasRangoFechaRule;
use Illuminate\Foundation\Http\FormRequest;

class FiltroAlbRequest extends FormRequest
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
            'tipo_id'   => ['required','integer'],
            'fpago_id'  => ['required','integer'],
            'fase_id'   => ['nullable','integer'],
            'fecha_d'   => ['required','date', new RangoFechaRule($this->fecha_d, $this->fecha_h)],
            'fecha_h'   => ['required','date', new MaxDiasRangoFechaRule($this->fecha_d, $this->fecha_h)],
            'quefecha'  => ['required'],
            'facturado' => ['required'],
            'reservas'  => ['boolean'],
            'depositos' => ['boolean']
        ];
    }
}
