<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;


class Etiqueta2Export implements FromView
{
    use Exportable;


    public function __construct($data)
    {
        $this->data = $data;
    }

    // public function getCsvSettings(): array
    // {
    //     return [
    //         'delimiter' => ';',
    //         'enclosure' => ''
    //     ];
    // }

    public function view(): View
    {

        return view('exports.etiqueta2', [
            'data' => $this->data,
        ]);

    }
}
