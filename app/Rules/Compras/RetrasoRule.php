<?php

namespace App\Rules\Compras;

use Illuminate\Contracts\Validation\Rule;

class RetrasoRule implements Rule
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
        return !($this->compra->retraso > 10 );
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Liquidar intereses antes';
    }
}
