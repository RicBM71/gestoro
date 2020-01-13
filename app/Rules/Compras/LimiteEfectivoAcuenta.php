<?php

namespace App\Rules\Compras;

use App\Deposito;
use Illuminate\Contracts\Validation\Rule;

class LimiteEfectivoAcuenta implements Rule
{
    protected $cliente_id;
    protected $fecha_deposito;
    protected $esEfectivo = array(6,8); // a cuenta + recuperado
    protected $concepto_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($cliente_id, $fecha, $concepto_id)
    {
        $this->cliente_id = $cliente_id;
        $this->fecha_deposito = $fecha;
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
        if (!in_array($this->concepto_id, $this->esEfectivo))
            return true;

        $imp = Deposito::valorAcuentaEnFecha($this->fecha_deposito, $this->cliente_id);

        if (($imp + $value) > session('parametros')->lim_efe)
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
        return 'El importe supera el límite de efectivo';
    }
}
