<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class OperacionesExport implements FromView
{

    use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
        $this->titulo = 'Operaciones';
    }

    public function view(): View
    {

        return view('exports.operaciones', [
            'data' => $this->data,
            'titulo' => $this->titulo,
        ]);

    }
}
