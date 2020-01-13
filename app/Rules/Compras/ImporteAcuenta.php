<?php

namespace App\Rules\Compras;

use Illuminate\Contracts\Validation\Rule;

class ImporteAcuenta implements Rule
{
    protected $compra;
    protected $tope_acuenta;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($compra)
    {
        $this->compra = $compra;
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
        $this->tope_acuenta = $this->compra->imp_recu - ($this->compra->importe_renovacion) * 2;

        if ($value >= $this->tope_acuenta)
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
        return 'Importe no puede ser mayor a '.getDecimal($this->tope_acuenta);
    }
}
