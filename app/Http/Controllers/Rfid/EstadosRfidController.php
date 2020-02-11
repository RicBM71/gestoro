<?php

namespace App\Http\Controllers\Rfid;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
}
