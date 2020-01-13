<?php

namespace App\Rules\Compras;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class FechaRecuperacion implements Rule
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

        if (auth()->user()->hasRole('Admin'))
            return true;

        $value = Carbon::parse($value);

        if ($value >= Carbon::parse($this->compra->fecha_bloqueo) && $value <= Carbon::today())
            return true;


        // if (auth()->user()->hasRole('Supervisor'))
        //     if ($value < Carbon::today()){
        //         $this->fecha_tope = Carbon::today();
        //         return false;
        //     }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Fecha fuera de rango';
    }
}
