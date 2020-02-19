<?php

namespace App\Rules\Albaranes;

use Illuminate\Contracts\Validation\Rule;

class RangoFechaFacturacionRule implements Rule
{
    protected $desde;
    protected $hasta;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($d, $h)
    {
        $this->desde = $d;
        $this->hasta = $h;
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
        return getEjercicio($this->desde) == getEjercicio($this->hasta);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El rango de ejercicio no es correcto';
    }
}
