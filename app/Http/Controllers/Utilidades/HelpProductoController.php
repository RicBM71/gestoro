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
use App\Scopes\EmpresaScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Scopes\EmpresaProductoScope;

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
                'empresas'  => Empresa::selEmpresas()->Venta()->whereIn('id',session('empresas_usuario'))->get(),
            ];

    }

    public function albaranes(Request $request){

        $data = $request->validate([
            'producto_id' => ['required', 'integer'],
        ]);

        //return Albalin::withOutGlobalScope(EmpresaScope::class)->with(['producto','albaran.fase'])->where('producto_id',$data['producto_id'])->get();

        $alb = DB::table('albalins')->select('albaran','serie_albaran','factura','serie_factura','notas', 'fecha_factura','fecha_albaran','fases.nombre AS fase', 'fases.color AS color', 'albaran_id','empresas.nombre AS empresa','albaranes.empresa_id')
                ->join('albaranes','albaran_id','=','albaranes.id')
                ->join('fases', 'fase_id','=','fases.id')
                ->join('empresas', 'albaranes.empresa_id','=','empresas.id')
                ->whereIn('albaranes.empresa_id', session('empresas_usuario'))
                ->where('producto_id',$data['producto_id'])
                ->whereNull('albaranes.deleted_at')
                ->get();
                //Albalin::withOutGlobalScope(EmpresaScope::class)->with(['albaran.fase'])->where('producto_id',$data['producto_id'])->get(),
        if (request()->wantsJson())
            return [
                'albalins'     =>  $alb,
            ];

    }

    public function find(Request $request){

        $data = $request->validate([
            'producto_id' => ['required', 'integer'],
        ]);

        if (request()->wantsJson())
            return [
                'producto'     =>  Producto::withOutGlobalScope(EmpresaProductoScope::class)->findOrFail($data['producto_id'])
            ];

    }


}
