<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;


class ComprasExport implements FromView
{
    use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
        $this->titulo = 'Compras';
    }

    public function view(): View
    {

        return view('exports.compras', [
            'data' => $this->data,
            'titulo' => $this->titulo,
        ]);

    }
}
