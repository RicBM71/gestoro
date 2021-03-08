<?php

namespace App\Http\Controllers\Exportar;

use Illuminate\Http\Request;
use App\Exports\ValorExiExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ValorExistenciasController extends Controller
{

    public function submit(Request $request){

        if (!auth()->user()->hasPermissionTo('consultas')){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'fecha_d'      => ['required','date'],
        ]);

        $data = DB::table('existencias')
                    ->select(DB::raw('nombre, detalle_id, importe'))
                    ->join('empresas','empresas.id','=','existencias.empresa_id')
                    ->whereIn('empresa_id', session('empresas_usuario'))
                    ->whereDate('fecha','=', $data['fecha_d'])
                    ->whereNull('deleted_at')
                    ->orderBy('nombre')
                    ->orderBy('detalle_id')
                    ->get();

        if (request()->wantsJson())
            return $data;


    }

    public function excel(Request $request){

        $items = $request->validate(['data'=>'array']);

        return Excel::download(new ValorExiExport($items['data']), 'ValorEx.xlsx');


    }


}
