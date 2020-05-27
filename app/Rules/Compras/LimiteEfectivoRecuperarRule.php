<?php

namespace App\Rules\Compras;

use App\Deposito;
use Illuminate\Contracts\Validation\Rule;

class LimiteEfectivoRecuperarRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($fecha, $compra, $concepto_id, $imp_total)
    {

        $this->compra = $compra;
        $this->concepto_id = $concepto_id;
        $this->fecha_deposito = $fecha;
        $this->imp_total = $imp_total;

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (auth()->user()->hasPermissionTo('salefe') || $value == 0)
            return true;

        if ($this->concepto_id == 10 && ($this->compra->importe + $this->compra->importe_recuperacion) >= session('parametros')->lim_efe)
            return false;

            // compruebo ante posibles recuperaciones en el mismo día.
        $imp = Deposito::valorAcuentaEnFecha($this->fecha_deposito, $this->compra->cliente_id);

        if ($this->concepto_id == 10 && ($imp + $this->imp_total) >= session('parametros')->lim_efe)
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
        return 'Recuperación supera límite efectivo';
    }
}
