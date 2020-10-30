<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ProductosExport implements FromView
{
    use Exportable;

    public function __construct($data, $titulo)
    {
        $this->data = $data;
        $this->titulo = $titulo;
    }

    public function view(): View
    {

        return view('exports.productos', [
            'data' => $this->data,
            'titulo' => $this->titulo,
        ]);

    }
}
