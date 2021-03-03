<?php

namespace App\Rules\Compras;

use App\Compra;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class FechaAmpliacion implements Rule
{
    protected $compra;
    protected $fecha_tope;
    protected $rango;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($compra_id)
    {
        $this->compra = Compra::findOrFail($compra_id);
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



        $value = Carbon::parse($value);

        $desde = Carbon::parse($this->compra->fecha_compra);

        $fecha_tope = Carbon::today();


        $this->rango = $desde->format('d/m/Y').' y '.$fecha_tope->format('d/m/Y');

        if ($value  < $desde)
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
        return "Fecha entre ".$this->rango;
    }
}
