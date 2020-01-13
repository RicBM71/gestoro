<?php

namespace App\Rules\Compras;

use App\Deposito;
use Illuminate\Contracts\Validation\Rule;

class ImporteMinRecuperacionRule implements Rule
{
    protected $compra;

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
        // saltarse esto supone que el valor de venta (recuperación) sería inferior al de compra.
        // dumpin, ojo!
        $totales = Deposito::totalesConcepto($this->compra->id);
        if (($totales[2]+$value) < $totales[0])
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
        return 'Importe de recuperación inferior al préstamo';
    }
}
