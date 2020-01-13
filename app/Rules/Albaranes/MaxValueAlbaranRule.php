<?php

namespace App\Rules\Albaranes;

use Illuminate\Contracts\Validation\Rule;

class MaxValueAlbaranRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($totales, $acuenta)
    {
        $this->resto = $totales['total'] - $acuenta;
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
        if ($value > $this->resto)
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
        return 'Importe entregado supera a venta '.$this->resto;
    }
}
