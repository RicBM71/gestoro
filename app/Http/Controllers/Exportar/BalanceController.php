<?php

namespace App\Http\Controllers\Exportar;

use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Exports\BalanceExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class BalanceController extends Controller
{
    public function balance(Request $request){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
            'operacion'=> ['required','string'],
        ]);

        if ($data['operacion'] == 'C')
            return $this->depositos($data['fecha_d'], $data['fecha_h']);
        else
            return $this->cobros($data['fecha_d'], $data['fecha_h']);

    }

    private function depositos($d, $h){


        $select=DB::getTablePrefix().'empresas.nombre AS empresa,'.
                DB::getTablePrefix().'conceptos.nombre AS concepto,signo, SUM(importe * signo) AS importe, COUNT(*) AS operaciones';

        $union1 = DB::table('depositos')
                    ->select(DB::raw($select))
                    ->join('empresas','depositos.empresa_id','=','empresas.id')
                    ->join('conceptos','depositos.concepto_id','=','conceptos.id')
                    ->whereIn('depositos.empresa_id', session('empresas_usuario'))
                    ->whereDate('fecha','>=', $d)
                    ->whereDate('fecha','<=', $h)
                    ->groupBy('empresa','concepto','signo');


        $select=DB::getTablePrefix().'empresas.nombre AS empresa,'.
                    '"xTOTALES" as concepto, signo, SUM(importe * signo) AS importe, COUNT(*) AS operaciones';

        $union2 = DB::table('depositos')
                    ->select(DB::raw($select))
                    ->join('empresas','depositos.empresa_id','=','empresas.id')
                    ->join('conceptos','depositos.concepto_id','=','conceptos.id')
                    ->whereIn('depositos.empresa_id', session('empresas_usuario'))
                    ->whereDate('fecha','>=', $d)
                    ->whereDate('fecha','<=', $h)
                    ->groupBy('empresa','concepto','signo')
                    ->union($union1)
                    ->orderBy('empresa')
                    ->orderBy('concepto')
                    ->get();


        return $union2;

    }

    private function cobros($d, $h){


        $select=DB::getTablePrefix().'empresas.nombre AS empresa,'.
                DB::getTablePrefix().'fpagos.nombre AS concepto, SUM(importe) AS importe, COUNT(*) AS operaciones';

        $union1 = DB::table('cobros')
                    ->select(DB::raw($select))
                    ->join('empresas','cobros.empresa_id','=','empresas.id')
                    ->join('fpagos','cobros.fpago_id','=','fpagos.id')
                    ->whereIn('cobros.empresa_id', session('empresas_usuario'))
                    ->whereDate('fecha','>=', $d)
                    ->whereDate('fecha','<=', $h)
                    ->whereNull('cobros.deleted_at')
                    ->groupBy('empresa','concepto');


        $select=DB::getTablePrefix().'empresas.nombre AS empresa,'.
                    '"xTOTALES" as concepto, SUM(importe) AS importe, COUNT(*) AS operaciones';

        $union2 = DB::table('cobros')
                    ->select(DB::raw($select))
                    ->join('empresas','cobros.empresa_id','=','empresas.id')
                    ->join('fpagos','cobros.fpago_id','=','fpagos.id')
                    ->whereIn('cobros.empresa_id', session('empresas_usuario'))
                    ->whereDate('fecha','>=', $d)
                    ->whereDate('fecha','<=', $h)
                    ->whereNull('cobros.deleted_at')
                    ->groupBy('empresa','concepto')
                    ->union($union1)
                    ->orderBy('empresa')
                    ->orderBy('concepto')
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

        return Excel::download(new BalanceExport($request->data), 'balance.xlsx');

    }
}
