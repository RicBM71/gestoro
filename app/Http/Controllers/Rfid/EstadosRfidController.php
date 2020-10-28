<?php

namespace App\Http\Controllers\Rfid;

use App\Recuento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Scopes\EmpresaProductoScope;

class EstadosRfidController extends Controller
{
    public function index(){


        if (!auth()->user()->hasRole('Gestor')){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Gestor');
        }

        $data = DB::table('productos')->select(DB::raw(DB::getTablePrefix().'empresas.nombre AS empresa,'.DB::getTablePrefix().'etiquetas.nombre AS estado, COUNT(*) AS registros '))
                    ->join('etiquetas','etiqueta_id','=','etiquetas.id')
                    ->join('empresas','empresa_id','=','empresas.id')
                    ->where('destino_empresa_id', session('empresa_id'))
                    ->where('estado_id', '<=', 3)
                    ->whereNull('productos.deleted_at')
                    ->groupBy('empresa','estado')
                    ->get();

        if (request()->wantsJson())
            return [
                'estados' => $data
            ];

    }

    public function baja(Request $request){

        if (!esAdmin())
            return abort(411,'No autorizado');

        $productos = DB::table('recuentos')->select('producto_id')
                        ->where('empresa_id', session('empresa_id'))
                        ->where('rfid_id', 3)
                        ->get();

        $productos = $productos->pluck('producto_id');

        $productos = $productos->toArray();

        $now = Carbon::now();


        DB::table('productos')
            ->whereIn('id', $productos)
            ->update(['deleted_at' => $now,
                      'updated_at' => $now,
                      'notas' => 'Recuento: Ref. Perdidas',
                      'username' => session('username')]);


        $data = Recuento::withOutGlobalScope(EmpresaProductoScope::class)
                    ->select('referencia','producto_id','productos.nombre AS nombre','precio_coste','rfids.nombre AS rfid','estados.nombre AS estado',
                                'productos.deleted_at', 'productos.notas', 'rfid_id', 'recuentos.id AS recuento_id', 'productos.empresa_id AS origen',
                                'productos.destino_empresa_id AS destino')
                    ->join('productos','productos.id','=','producto_id')
                    ->join('rfids','rfids.id','=','rfid_id')
                    ->join('estados','estados.id','=','productos.estado_id')
                    ->where('recuentos.empresa_id', session('empresa_id'))
                    ->get();

        if (request()->wantsJson())
                return $data;
            }
}
