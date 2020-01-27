<?php

namespace App\Rules\Albaranes;

use Illuminate\Contracts\Validation\Rule;

class LimiteEfectivoRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($totales, $fpago_id, $iva_no_residente)
    {
        $this->total = $totales['total'];
        $this->fpago_id = $fpago_id;
        $this->limite_efectivo = $iva_no_residente ?  session('parametros')->lim_efe_nores :  session('parametros')->lim_efe;
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
        $value = round($value, 2);
        if (auth()->user()->hasPermissionTo('salefe'))
            return true;

        if ($this->fpago_id == 1 && $this->total >= $this->limite_efectivo)
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
        return 'Supera límite efectivo '.getDecimal($this->limite_efectivo,0);
    }
}
