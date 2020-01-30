<?php

namespace App\Http\Controllers\Utilidades;

use App\Fase;
use App\Tipo;
use App\Fpago;
use App\Cliente;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpFiltroAlbController extends Controller
{
    public function index(){

        if (request()->wantsJson())
            return [
                'fpagos'=> Fpago::selFPagos(),
                'fases' => Fase::selFases('V'),
                //'tipos' => Tipo::selTiposVen(),
                'tipos'  => Tipo::selTiposWithContador(),
                'asociados'=> Cliente::selAsociados()
            ];

    }
}
