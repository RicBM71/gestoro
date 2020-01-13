<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PrecioCosteRule implements Rule
{
    protected $coste;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($coste)
    {
        $this->coste = $coste;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $precio_venta)
    {

        if ($this->coste == 0) return true;

        return ($precio_venta >= $this->coste);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Precio venta inferior a coste';
    }
}
