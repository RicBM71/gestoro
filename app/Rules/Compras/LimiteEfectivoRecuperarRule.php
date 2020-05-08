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
    public function __construct($cliente_id, $fecha, $imp_total)
    {

        $this->cliente_id = $cliente_id;
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

        $imp = Deposito::valorAcuentaEnFecha($this->fecha_deposito, $this->cliente_id);

        if (($imp + $this->imp_total) > session('parametros')->lim_efe)
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
        return 'Supera límite efectivo';
    }
}
