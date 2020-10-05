<?php

namespace App\Http\Controllers\Utilidades;

use App\Fase;
use App\Tipo;
use App\Clase;
use App\Grupo;
use App\Libro;
use App\Marca;
use App\Estado;
use App\Almacen;
use App\Empresa;
use App\Categoria;
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
                'tipos' => Tipo::selTiposCom(),
                'libro_def' =>  Libro::distinct()->first(),
                'almacenes' => Almacen::selAlmacenes(),
            ];

    }

    public function clases($grupo_id){

        if (request()->wantsJson())
            return [
                'clases'=> Clase::selClases($grupo_id),
                'empresas' => Empresa::selEmpresas()->Venta()->get(),
                'marcas'    => Marca::selMarcas(),
                'categorias'=> Categoria::selCategorias(),
            ];

    }


}
