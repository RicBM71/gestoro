<?php

namespace App\Rules\Compras;

use App\Hcompra;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class ReabrirLoteRule implements Rule
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

        if (hasReaCom()) return true;

        if (Carbon::parse($this->compra->fecha_compra) == Carbon::today()){
            if (esPropietario($this->compra) )
                return true;
        }

        $cambio_interes = Hcompra::getCambios($this->compra->id);

        if (hasEdtInt() && $cambio_interes == 0) return true;

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Usuario no autorizado a reabrir compra!';
    }
}
