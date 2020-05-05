<?php

namespace App\Rules\Compras;

use Illuminate\Contracts\Validation\Rule;

class ImporteRecuperacion implements Rule
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

        if ($value > $this->compra->imp_recu)
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
        return 'Importe recuperación supera al de compra - admin';
    }
}
