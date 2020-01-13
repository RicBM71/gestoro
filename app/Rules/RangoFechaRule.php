<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class RangoFechaRule implements Rule
{

    protected $desde;
    protected $hasta;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($d, $h)
    {
        $this->desde = $d;
        $this->hasta = $h;
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
        return !(Carbon::parse($this->desde) > Carbon::parse($this->hasta));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El rango de fechas no es correcto!';
    }
}
