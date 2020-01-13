<?php

namespace App\Http\Controllers\Utilidades;

use App\Fase;
use App\Http\Controllers\Controller;

class HelpFasesController extends Controller
{
    public function compra()
    {
        if (request()->wantsJson())
            return [
                'fases' => Fase::selFases('C'),
            ];

    }

    public function venta()
    {
        if (request()->wantsJson())
            return [
                'fases' => Fase::selFases('V'),
            ];

    }

}
