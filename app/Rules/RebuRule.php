<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class RebuRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($estado_id)
    {
        $this->estado_id = $estado_id;
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
        if ($value == 2 && $this->estado_id > 4)
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
        return 'Tipo de IVA no coincide con estado asignado';
    }
}
