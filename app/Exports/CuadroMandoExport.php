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
                                $data_net,
                                $data_ven,
                                $data_dep,
                                $data_pro)
    {
        $this->data_com = $data_com;
        $this->data_liq = $data_liq;
        $this->data_inv = $data_inv;
        $this->data_net = $data_net;
        $this->data_ven = $data_ven;
        $this->data_dep = $data_dep;
        $this->data_pro = $data_pro;
    }

    public function view(): View
    {
        return view('exports.mando', [
            'data_com' => $this->data_com,
            'data_liq' => $this->data_liq,
            'data_inv' => $this->data_inv,
            'data_net' => $this->data_net,
            'data_ven' => $this->data_ven,
            'data_dep' => $this->data_dep,
            'data_pro' => $this->data_pro,
            'det_ven'  => [
                '3' => 'VENTAS REBU',
                '4' => 'VENTAS RG',
                '5' => 'TALLER'
            ],
            'det_com'  => [
                '1' => 'DEPOSITO RECOMPRAS (AC)',
                '2' => 'DEPOSITO COMPRAS (AC)'
            ],
            'titulo' => 'Cuadro de mando',
        ]);

    }
}
