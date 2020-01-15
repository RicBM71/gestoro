<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;


class DetalleVentasExport implements FromView
{
    use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
        $this->titulo = 'Detalle Operaciones de venta';
    }

    public function view(): View
    {

        return view('exports.detaven', [
            'data' => $this->data,
            'titulo' => $this->titulo,
        ]);

    }
}
