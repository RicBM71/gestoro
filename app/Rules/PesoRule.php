<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PesoRule implements Rule
{

    protected $peso;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($peso)
    {
        $this->peso = $peso;

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

        if ($this->peso == false) // no requiere peso
            return true;

        if ($value <= 0) return false;

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Indicar peso > 0';
    }
}
