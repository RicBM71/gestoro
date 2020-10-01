<?php

namespace App\Rules\Albaranes;

use App\Albalin;
use Illuminate\Contracts\Validation\Rule;

class StockRule implements Rule
{
    protected $producto_id;
    protected $albalin_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($producto_id, $albalin_id=0)
    {
        $this->producto_id = $producto_id;
        $this->albalin_id = $albalin_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $unidades
     * @return bool
     */
    public function passes($attribute, $unidades)
    {

        $validar = Albalin::validarStock($this->producto_id, $this->albalin_id);

        if ($validar === false){
            return true;
        }

        $stock = $validar - $unidades;

        return  $stock >= 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'No hay stock suficiente';
    }
}
