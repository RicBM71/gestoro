<?php

namespace App\Rules\Albaranes;

use App\Producto;
use Illuminate\Contracts\Validation\Rule;

class UnidadesRule implements Rule
{
    protected $producto;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($producto_id)
    {
        $this->producto = Producto::with('clase')->findOrFail($producto_id);
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

        // if (in_array($this->producto->estado_id, [5,6]) && $value <= 0)
        //     return false;
        if ($this->producto->univen == "U" && $value <= 0)
            return false;

        if ($this->producto->clase->peso && $value <= 0)
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
        return 'Indicar un valor mayor a uno';
    }
}
