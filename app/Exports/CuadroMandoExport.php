<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class CuadroMandoExport implements FromView
{

    use Exportable;

    public function __construct($data_com,
                                $data_liq,
                                $data_inv,
                                $data_ven,
                                $data_dep)
    {
        $this->data_com = $data_com;
        $this->data_liq = $data_liq;
        $this->data_inv = $data_inv;
        $this->data_ven = $data_ven;
        $this->data_dep = $data_dep;
    }

    public function view(): View
    {
        return view('exports.mando', [
            'data_com' => $this->data_com,
            'data_liq' => $this->data_liq,
            'data_inv' => $this->data_inv,
            'data_ven' => $this->data_ven,
            'data_dep' => $this->data_dep,
            'det_ven'  => [
                '3' => 'VENTAS REBU',
                '4' => 'VENTAS RG',
                '5' => 'TALLER'
            ],
            'det_com'  => [
                '1' => 'RECOMPRAS',
                '2' => 'COMPRAS'
            ],
            'titulo' => 'Cuadro de mando',
        ]);

    }
}
