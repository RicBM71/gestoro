<?php

namespace App\Exports;

use App\Extracto;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ExtractosExport implements FromView
{

    use Exportable;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function view(): View
    {

        return view('exports.extractos', [
            'extractos' => Extracto::query()->whereYear('fecha',$this->year)->get()
        ]);

    }
}
