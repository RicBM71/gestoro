<?php

namespace App\Http\Controllers\Utilidades;

use App\Clase;
use App\Grupo;
use App\Estado;
use App\Albalin;
use App\Cliente;
use App\Empresa;
use App\Quilate;
use App\Producto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpProductoController extends Controller
{

    /**
     *
     * Deveuelve productos, ref. y nombres por compra
     */
    public function vendibles(Request $request)
    {

        $data = $request->validate([
            'tipo_id' => ['required', 'integer'],
            'referencia' => ['nullable'],
        ]);

       //
            //$iva_id =  2 : 1;

        if (request()->wantsJson())
            if ($data['tipo_id'] == 3)
                return Producto::productosREBU($data['referencia']);
            else
                return Producto::productosGenericos($data['referencia']);


    }

    public function producto(Request $request){

        $data = $request->validate([
            'producto_id' => ['required', 'integer'],
        ]);

        if (request()->wantsJson())
            return Producto::with(['iva','clase'])->findOrFail($data['producto_id']);

    }

    public function filtro(){

        if (request()->wantsJson())
            return [
                'grupos'    => Grupo::SelGrupos(),
                'clases'    => Clase::selGrupoClase(),
                'estados'   => Estado::selEstados(),
                'asociados' => Cliente::selAsociados(),
                'quilates'  => Quilate::selQuilates(),
                'empresas'  => Empresa::selEmpresas()->Venta()->get(),
            ];

    }

    public function albaranes(Request $request){

        $data = $request->validate([
            'producto_id' => ['required', 'integer'],
        ]);

        if (request()->wantsJson())
            return Albalin::with(['productos','albaran.fase'])->where('producto_id',$data['producto_id'])->get();

    }
}
