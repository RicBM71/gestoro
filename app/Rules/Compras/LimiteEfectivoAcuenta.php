<?php

namespace App\Rules\Compras;

use App\Deposito;
use Illuminate\Contracts\Validation\Rule;

class LimiteEfectivoAcuenta implements Rule
{
    protected $cliente_id;
    protected $fecha_deposito;
    protected $esEfectivo = array(7,10); // a cuenta + recuperado
    protected $compra;
    protected $concepto_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($compra, $concepto_id)
    {

        $this->compra = $compra;
        $this->concepto_id = $concepto_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value (el valor a cuenta)
     * @return bool
     */
    public function passes($attribute, $value)
    {

        if (auth()->user()->hasPermissionTo('salefe'))
            return true;

            // se va a guardar el importe como por banco
        //if (!in_array($this->concepto_id, $this->esEfectivo))
        if ($this->concepto_id != 7)
            return true;

        if (($this->compra->importe + $this->compra->importe_recuperacion) >= session('parametros')->lim_efe)

        // $imp = Deposito::valorAcuentaEnFecha($this->fecha_deposito, $this->cliente_id);

        // if (($imp + $value) > session('parametros')->lim_efe)
            return false;

        return true;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El importe supera el límite de efectivo ';
    }
}
