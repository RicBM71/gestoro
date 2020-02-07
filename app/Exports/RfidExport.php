<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;


class RfidExport implements FromView, WithCustomCsvSettings
{
    use Exportable;


    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'enclosure' => ''
        ];
    }

    public function view(): View
    {

        return view('exports.rfid', [
            'data' => $this->data,
        ]);

    }
}
