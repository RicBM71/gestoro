<?php

namespace App\Http\Controllers\Exportar;

use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Exports\OperacionesExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class OperacionesController extends Controller
{
    public function operaciones(Request $request){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
            'operacion'=> ['required','string'],
        ]);

        if ($data['operacion'] == 'C') // de momento solo compras
            return $this->compras($data['fecha_d'], $data['fecha_h']);
        else
            return $this->ventas($data['fecha_d'], $data['fecha_h']);

    }

    private function compras($d, $h){

        $select=DB::getTablePrefix().'empresas.nombre AS empresa,'.
                DB::getTablePrefix().'tipos.nombre AS tipo,'.
                DB::getTablePrefix().'fases.nombre AS fase,'.
                DB::getTablePrefix().'clases.nombre AS clase,'.
                DB::getTablePrefix().'comlines.quilates AS quilates,'.
                ' SUM('.DB::getTablePrefix().'comlines.importe) AS importe, COUNT(*) AS operaciones, SUM(peso_gr) AS peso';

        $union1 = DB::table('compras')
                    ->select(DB::raw($select))
                    ->join('empresas','compras.empresa_id','=','empresas.id')
                    ->join('comlines','compras.id','=','compra_id')
                    ->join('fases','fase_id','=','fases.id')
                    ->join('tipos','tipo_id','=','tipos.id')
                    ->join('clases','clase_id','=','clases.id')
                    ->whereIn('compras.empresa_id', session('empresas_usuario'))
                    ->whereDate('fecha_compra','>=', $d)
                    ->whereDate('fecha_compra','<=', $h)
                    ->groupBy('empresa','tipo', 'fase', 'clase', 'quilates');

        $select=DB::getTablePrefix().'empresas.nombre AS empresa,'.
                    '"TOTAL" AS tipo,'.
                    '"" AS fase,'.
                    '"" AS clase,'.
                    '0 AS quilates,'.
                    ' SUM('.DB::getTablePrefix().'comlines.importe) AS importe, COUNT(*) AS operaciones, 0 AS peso';

        $union2 = DB::table('compras')
                    ->select(DB::raw($select))
                    ->join('empresas','compras.empresa_id','=','empresas.id')
                    ->join('comlines','compras.id','=','compra_id')
                    ->join('fases','fase_id','=','fases.id')
                    ->join('tipos','tipo_id','=','tipos.id')
                    ->join('clases','clase_id','=','clases.id')
                    ->whereIn('compras.empresa_id', session('empresas_usuario'))
                    ->whereDate('fecha_compra','>=', $d)
                    ->whereDate('fecha_compra','<=', $h)
                    ->union($union1)
                    ->groupBy('empresa','tipo', 'fase', 'clase', 'quilates')
                    ->orderBy('empresa')
                    ->orderBy('tipo')
                    ->orderBy('fase')
                    ->orderBy('clase')
                    ->orderBy('quilates')
                    ->get();

        return $union2;

    }

    private function ventas($d, $h){

        $select=DB::getTablePrefix().'empresas.nombre AS empresa,'.
                DB::getTablePrefix().'tipos.nombre AS tipo,'.
                DB::getTablePrefix().'fases.nombre AS fase,'.
                DB::getTablePrefix().'clases.nombre AS clase,'.
                '0 AS quilates,'.
                ' SUM('.DB::getTablePrefix().'albalins.importe_venta) AS importe, COUNT(*) AS operaciones, SUM(peso_gr) AS peso';

        $union1 = DB::table('albaranes')
                    ->select(DB::raw($select))
                    ->join('empresas','albaranes.empresa_id','=','empresas.id')
                    ->join('albalins','albaranes.id','=','albaran_id')
                    ->join('productos','productos.id','=','producto_id')
                    ->join('fases','fase_id','=','fases.id')
                    ->join('tipos','tipo_id','=','tipos.id')
                    ->join('clases','clase_id','=','clases.id')
                    ->whereIn('albaranes.empresa_id', session('empresas_usuario'))
                    ->whereDate('fecha_albaran','>=', $d)
                    ->whereDate('fecha_albaran','<=', $h)
                    ->whereNull('albaranes.deleted_at')
                    ->groupBy('empresa','tipo', 'fase', 'clase', 'quilates');

        $select=DB::getTablePrefix().'empresas.nombre AS empresa,'.
                    '"TOTAL" AS tipo,'.
                    '"" AS fase,'.
                    '"" AS clase,'.
                    '0 AS quilates,'.
                    ' SUM('.DB::getTablePrefix().'albalins.importe_venta) AS importe, COUNT(*) AS operaciones, 0 AS peso';

        $union2 = DB::table('albaranes')
                    ->select(DB::raw($select))
                    ->join('empresas','albaranes.empresa_id','=','empresas.id')
                    ->join('albalins','albaranes.id','=','albaran_id')
                    ->join('productos','productos.id','=','producto_id')
                    ->join('fases','fase_id','=','fases.id')
                    ->join('tipos','tipo_id','=','tipos.id')
                    ->join('clases','clase_id','=','clases.id')
                    ->whereIn('albaranes.empresa_id', session('empresas_usuario'))
                    ->whereDate('fecha_albaran','>=', $d)
                    ->whereDate('fecha_albaran','<=', $h)
                    ->whereNull('albaranes.deleted_at')
                    ->union($union1)
                    ->groupBy('empresa','tipo', 'fase', 'clase', 'quilates')
                    ->orderBy('empresa')
                    ->orderBy('tipo')
                    ->orderBy('fase')
                    ->orderBy('clase')
                    ->orderBy('quilates')
                    ->get();

        return $union2;

    }



     /**
     * Recibe las facturas por request, previamente de $this->lisfac()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new OperacionesExport($request->data), 'operaciones.xlsx');

    }
}
