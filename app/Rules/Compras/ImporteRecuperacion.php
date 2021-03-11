<?php

namespace App\Rules\Compras;

use Illuminate\Contracts\Validation\Rule;

class ImporteRecuperacion implements Rule
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
        //dejo importe mayor de recuperación por si hay acuenta (por recuperacion efe+tar)
        // \Log::info($this->imp_total_recu);
        // \Log::info($this->compra->imp_recu);
        if (hasReaCom())
            return true;

        if ($this->imp_total_recu > $this->compra->imp_recu)
            return false();

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Importe recuperación supera al de compra - admin';
    }
}
