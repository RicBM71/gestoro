<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class CajaExport implements FromView
{

    use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {

        return view('exports.caja', [
            'data' => $this->data,
        ]);

    }
}
