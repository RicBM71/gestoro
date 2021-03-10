<?php

namespace App\Rules\Compras;

use App\Deposito;
use Illuminate\Contracts\Validation\Rule;

class ImporteMinRecuperacionRule implements Rule
{
    protected $compra;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($compra, $imp_total_recu)
    {
        $this->compra = $compra;
        $this->imp_total_recu = $imp_total_recu;
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

        $totales = Deposito::totalesConcepto($this->compra->id);
        // \Log::info($totales);
        // \Log::info($this->imp_total_recu);
        // \Log::info($this->compra->imprecu);
        // \Log::info($this->compra->toArray());


        if ($this->imp_total_recu < $this->compra->imprecu)
            return hasReaCom();

        // // saltarse esto supone que el valor de venta (recuperación) sería inferior al de compra.
        // // dumpin, ojo! OJO PORQUE FALLA SI LO DEJAMOS PASAR, HAY REVISAR EN OBSERVER IMPORTE PRESTAMO
        // if (($totales[2]+$this->imp_total_recu) <  $this->compra->imp_recu)
        //     return false;

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Importe de recuperación inferior al préstamo';
    }
}
