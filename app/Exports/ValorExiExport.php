<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ValorExiExport implements FromView
{

    use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
        $this->titulo = 'Valor Existencias';
    }

    public function view(): View
    {

        return view('exports.valorex', [
            'data' => $this->data,
            'titulo' => $this->titulo,
        ]);

    }
}
