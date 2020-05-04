<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class RecogidasExport implements FromView
{
    use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {

        return view('exports.recogidas', [
            'data' => $this->data,
        ]);

    }
}
