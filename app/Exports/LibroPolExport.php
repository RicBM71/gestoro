<?php

namespace App\Exports;

use App\Compra;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class LibroPolExport implements FromView, WithCustomCsvSettings
{

    use Exportable;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->plantilla_excel = $data['plantilla_excel'];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'use_bom'=>true
        ];
    }

    public function view(): View
    {

        return view('exports.'.$this->plantilla_excel, [
            'compras' => Compra::getLibroPolExcel($this->data),
            'codigo_pol' => $this->data['codigo_pol'],
            'establecimiento' => $this->data['establecimiento']
        ]);

    }
}
