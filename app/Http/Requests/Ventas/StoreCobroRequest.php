<?php

namespace App\Http\Requests\Ventas;

use App\Cobro;
use App\Albalin;
use App\Albaran;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Albaranes\MaxValueAlbaranRule;
use App\Rules\Albaranes\LimiteCobroFechaRule;
use App\Rules\Albaranes\LimiteEfectivoRule;

class StoreCobroRequest extends FormRequest
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

        $albaran = Albaran::findOrFail($this->albaran_id);
        $acuenta = Cobro::getAcuentaByAlbaran($this->albaran_id);
        $totales = Albalin::totalAlbaranByAlb($this->albaran_id);

        $data = [

            'fpago_id'      => ['required','integer','between:1,4'],
            'fecha'         => ['required','date'],
            'albaran_id'    => ['required', 'integer'],
            'cliente_id'    => ['required', 'integer'],
            'importe'       => ['required', 'numeric',
                                    new MaxValueAlbaranRule($totales,$acuenta),
                                    new LimiteEfectivoRule($totales, $this->fpago_id, $albaran->iva_no_residente),
                                    new LimiteCobroFechaRule($this->cliente_id, $this->fecha, $this->fpago_id, $albaran->iva_no_residente)],
            'notas'         => ['nullable','max:50'],
        ];

        return $data;
    }
}
