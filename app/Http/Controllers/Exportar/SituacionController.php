<?php

namespace App\Http\Controllers\Exportar;

use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Exports\SituacionExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class SituacionController extends Controller
{

    public function situacion(Request $request){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
            'operacion'=> ['required','string'],
        ]);

        return $this->detalle($data);

    }

    private function detalle($data){
        $select='"%c" AS concepto, "%d" AS dh, SUM('.DB::getTablePrefix().'depositos.importe) AS importe';


        $s0 = str_replace('%c',"COMPRAS", $select);
        $s0 = str_replace('%d',"D", $s0);
        $union0 = DB::table('depositos')
            ->select(DB::raw($s0))
            ->join('compras', 'depositos.compra_id', '=','compras.id')
            ->join('conceptos','depositos.concepto_id','=','conceptos.id')
            ->where('depositos.empresa_id', session('empresa')->id)
            ->where('tipo_id', 2)
            ->whereIn('concepto_id', [1,2,3,13,14,15])
            ->whereDate('fecha','>=', $data['fecha_d'])
            ->whereDate('fecha','<=', $data['fecha_h'])
            ->groupBy('concepto','dh');

        $s0 = str_replace('%c',"RECOMPRAS", $select);
        $s0 = str_replace('%d',"D", $s0);
        $union0b = DB::table('depositos')
            ->select(DB::raw($s0))
            ->join('compras', 'depositos.compra_id', '=','compras.id')
            ->join('conceptos','depositos.concepto_id','=','conceptos.id')
            ->where('depositos.empresa_id', session('empresa')->id)
            ->where('tipo_id', 1)
            ->whereIn('concepto_id', [1,2,3,13,14,15])
            ->whereDate('fecha','>=', $data['fecha_d'])
            ->whereDate('fecha','<=', $data['fecha_h'])
            ->groupBy('concepto','dh');

        $s1 = str_replace('%c',"AMP./CAPITAL", $select);
        $s1 = str_replace('%d',"D", $s1);
        $union1 = DB::table('depositos')
            ->select(DB::raw($s1))
            ->join('conceptos','depositos.concepto_id','=','conceptos.id')
            ->where('empresa_id', session('empresa')->id)
            ->whereIn('concepto_id', [16,17,18])
            ->whereDate('fecha','>=', $data['fecha_d'])
            ->whereDate('fecha','<=', $data['fecha_h'])
            ->groupBy('concepto','dh');

        $s1 = str_replace('%c',"AMPLIACIONES", $select);
        $s1 = str_replace('%d',"H", $s1);
        $union2 = DB::table('depositos')
            ->select(DB::raw($s1))
            ->join('conceptos','depositos.concepto_id','=','conceptos.id')
            ->where('empresa_id', session('empresa')->id)
            ->whereIn('concepto_id', [4,5,6])
            ->whereDate('fecha','>=', $data['fecha_d'])
            ->whereDate('fecha','<=', $data['fecha_h'])
            ->groupBy('concepto','dh');

        $s1 = str_replace('%c',"A CUENTA", $select);
        $s1 = str_replace('%d',"H", $s1);
        $union3 = DB::table('depositos')
            ->select(DB::raw($s1))
            ->join('conceptos','depositos.concepto_id','=','conceptos.id')
            ->where('empresa_id', session('empresa')->id)
            ->whereIn('concepto_id', [7,8,9])
            ->whereDate('fecha','>=', $data['fecha_d'])
            ->whereDate('fecha','<=', $data['fecha_h'])
            ->groupBy('concepto','dh');

        $s1 = str_replace('%c',"RECUPERACIONES", $select);
        $s1 = str_replace('%d',"H", $s1);
        $union4 = DB::table('depositos')
            ->select(DB::raw($s1))
            ->join('conceptos','depositos.concepto_id','=','conceptos.id')
            ->where('empresa_id', session('empresa')->id)
            ->whereIn('concepto_id', [10,11,12])
            ->whereDate('fecha','>=', $data['fecha_d'])
            ->whereDate('fecha','<=', $data['fecha_h'])
            ->groupBy('concepto','dh');


        $select='"%c" AS concepto, "%d" AS dh, SUM(importe) AS importe';
        $s1 = str_replace('%c',"VENTAS REBU", $select);
        $s1 = str_replace('%d',"H", $s1);
        $union5 = DB::table('cobros')
                ->select(DB::raw($s1))
                ->join('albaranes','albaranes.id','=','cobros.albaran_id')
                ->join('fpagos','cobros.fpago_id','=','fpagos.id')
                ->where('albaranes.empresa_id', session('empresa')->id)
                ->where('albaranes.tipo_id', 3)
                ->whereDate('cobros.fecha','>=', $data['fecha_d'])
                ->whereDate('cobros.fecha','<=', $data['fecha_h'])
                // ->whereDate('fecha_factura','>=', $data['fecha_d'])
                // ->whereDate('fecha_factura','<=', $data['fecha_h'])
                ->whereNull('albaranes.deleted_at')
                ->groupBy('concepto','dh');

        // $select='"%c" AS concepto, "%d" AS dh, SUM(precio_coste) AS importe';
        // $s1 = str_replace('%c',"COSTE REBU", $select);
        // $s1 = str_replace('%d',"D", $s1);
        // $union5b = DB::table('albaranes')
        //         ->select(DB::raw($s1))
        //         ->join('albalins','albaranes.id','=','albalins.albaran_id')
        //         ->where('albaranes.empresa_id', session('empresa')->id)
        //         ->where('albaranes.tipo_id', 3)
        //         ->whereDate('fecha_factura','>=', $data['fecha_d'])
        //         ->whereDate('fecha_factura','<=', $data['fecha_h'])
        //         ->whereNull('albaranes.deleted_at')
        //         ->groupBy('concepto','dh');

        $select='"%c" AS concepto, "%d" AS dh, SUM(precio_coste) AS importe';
        $s1 = str_replace('%c',"NUEVOS PRODUCTOS (P. Coste)", $select);
        $s1 = str_replace('%d',"H", $s1);
        $union7 = DB::table('productos')
                ->select(DB::raw($s1))
                ->where('empresa_id', session('empresa')->id)
                ->where('estado_id', '<=', 2)
                ->whereDate('created_at','>=', $data['fecha_d'])
                ->whereDate('created_at','<=', $data['fecha_h'])
                ->whereNull('deleted_at')
                ->groupBy('concepto','dh');

        $select='"%c" AS concepto, "%d" AS dh, SUM(importe) AS importe';
        $s1 = str_replace('%c',"VENTAS R.G.", $select);
        $s1 = str_replace('%d',"H", $s1);
        $union6 = DB::table('cobros')
                ->select(DB::raw($s1))
                ->join('albaranes','albaranes.id','=','cobros.albaran_id')
                ->join('fpagos','cobros.fpago_id','=','fpagos.id')
                ->where('albaranes.empresa_id', session('empresa')->id)
                ->where('albaranes.tipo_id', 4)
                ->whereDate('cobros.fecha','>=', $data['fecha_d'])
                ->whereDate('cobros.fecha','<=', $data['fecha_h'])
                // ->whereDate('fecha_factura','>=', $data['fecha_d'])
                // ->whereDate('fecha_factura','<=', $data['fecha_h'])
                ->whereNull('albaranes.deleted_at')
                ->groupBy('concepto','dh')
                ->union($union0)
                ->union($union0b)
                ->union($union1)
                ->union($union2)
                ->union($union3)
                ->union($union4)
                ->union($union5)
                // ->union($union5b)
                ->union($union7)
                ->orderBy('importe','desc')
                ->get();

        return $union6;
    }

     /**
     * Recibe las facturas por request, previamente de $this->lisfac()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new SituacionExport($request->data, 'Resumen '.session('empresa')->razon), 'resumen.xlsx');

    }
}
