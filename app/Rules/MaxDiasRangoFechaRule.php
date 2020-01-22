<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class MaxDiasRangoFechaRule implements Rule
{
    protected $desde;
    protected $hasta;
    protected $max_dias=366;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($d, $h)
    {
        $this->desde = $d;
        $this->hasta = $h;

        if (esRoot())
            $this->max_dias = 9999;
        elseif (esAdmin())
            $this->max_dias = 366 * 2;
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
        return !(Carbon::parse($this->desde)->diffInDays(Carbon::parse($this->hasta)) > $this->max_dias);
    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Rango fechas excede '.$this->max_dias.' días';
    }
}
