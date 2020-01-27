<?php

namespace App\Rules\Albaranes;

use App\Cobro;
use Illuminate\Contracts\Validation\Rule;

class LimiteCobroFechaRule implements Rule
{
    protected $cliente_id;
    protected $fecha;
    protected $fpago_id;
    protected $limite_efectivo;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($cliente_id, $fecha, $fpago_id, $iva_no_residente)
    {
        $this->cliente_id = $cliente_id;
        $this->fecha = $fecha;
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
        
        if ($this->fpago_id != 1 || $this->cliente_id == 1)
            return true;

        if (auth()->user()->hasPermissionTo('salefe'))
            return true;


        $total_cobrado = Cobro::getAcuentaByClienteFecha($this->cliente_id, $this->fecha);

        if (($total_cobrado + $value) >= $this->limite_efectivo)
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
        return 'Excede límite diario '.getDecimal($this->limite_efectivo,0).'€';
    }
}
