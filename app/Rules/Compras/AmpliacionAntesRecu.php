<?php

namespace App\Rules\Compras;

use App\Deposito;
use Illuminate\Contracts\Validation\Rule;

class AmpliacionAntesRecu implements Rule
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
        return Deposito::getAcuentaEnFecha($this->compra->id, $value) > 0 ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'No se permite entregar a cuenta y recuperar el mismo día.';
    }
}
