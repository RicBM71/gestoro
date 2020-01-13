<?php

namespace App\Rules\Albaranes;

use App\Albalin;
use App\Producto;
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
    public function __construct($producto_id, $albalin_id)
    {
        $this->producto_id = $producto_id;
        $this->albalin_id = $albalin_id;

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

            // si no hay control de stock me lo salto.
        if (session('empresa')->getFlag(5) == 0)
            return true;


        $producto = Producto::findOrfail($this->producto_id);

        if ($producto->stock == 1)  // si solo hay 1 de stock, no hay control del stock.
            return true;

        $vendidas = Albalin::where('id', '<>', $this->albalin_id)
                            ->where('producto_id', $this->producto_id)
                            ->whereNull('deleted_at')
                            ->get()->sum('unidades');

        if (($vendidas + $value) > $producto->stock)
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
        return 'No hay stock suficiente';
    }


}
