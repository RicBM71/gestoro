<?php

namespace App\Http\Controllers\Utilidades;

use App\Fase;
use App\Tipo;
use App\Clase;
use App\Grupo;
use App\Estado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpGruposController extends Controller
{
    public function index(Request $request)
    {
        if (request()->wantsJson())
            return [
                'grupos'=> Grupo::selGruposRebu(),
                'fases' => Fase::selFases('C'),
                'tipos' => Tipo::selTiposCom()
            ];

    }

    public function clases($grupo_id){

        if (request()->wantsJson())
            return [
                'clases'=> Clase::selClases($grupo_id),
            ];

    }


}
