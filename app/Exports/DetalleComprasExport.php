<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;


class DetalleComprasExport implements FromView
{
    use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
        $this->titulo = 'Detalle Operaciones de compra';
    }

    public function view(): View
    {

        return view('exports.detacom', [
            'data' => $this->data,
            'titulo' => $this->titulo,
        ]);

    }
}
