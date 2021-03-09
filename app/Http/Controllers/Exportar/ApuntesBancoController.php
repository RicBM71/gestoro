<?php

namespace App\Http\Controllers\Exportar;

use App\Tipo;
use App\Deposito;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use Illuminate\Support\Facades\DB;
use App\Exports\ApuntesBancoExport;
use App\Exports\MetalDepositoExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ApuntesBancoController extends Controller
{
    public function index(){


        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        // if (request()->wantsJson())
        //     return [
        //         'tipo' => Tipo::selTipos(),
        //     ];

    }

    public function submit(Request $request){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
            'operacion'  => ['required','string'],
        ]);

        if ($data['operacion']=='C')
            return $this->compras($data);
        else
            return $this->ventas($data);

    }

    private function compras($data){

        return DB::table('depositos')
             ->select('depositos.fecha','conceptos.nombre AS concepto','depositos.importe','depositos.notas AS notas','compras.albaran','fecha_compra AS fecha_op','serie_com AS serie','razon')
             ->join('conceptos','conceptos.id','=','concepto_id')
             ->join('compras','compras.id','=','compra_id')
             ->join('clientes','clientes.id','=','depositos.cliente_id')
             ->where('depositos.empresa_id',session('empresa_id'))
             ->whereIn('concepto_id', [2,3,5,6,8,9,11,12,17,18,14,15])
             ->whereDate('fecha','>=', $data['fecha_d'])
             ->whereDate('fecha','<=', $data['fecha_h'])
             ->get();

     }

     private function ventas($data){

        return DB::table('cobros')
             ->select('cobros.fecha','fpagos.nombre AS concepto','cobros.importe','cobros.notas AS notas','albaranes.albaran','fecha_albaran AS fecha_op','serie_albaran AS serie','razon')
             ->join('fpagos','fpagos.id','=','fpago_id')
             ->join('albaranes','albaranes.id','=','albaran_id')
             ->join('clientes','clientes.id','=','cobros.cliente_id')
             ->where('cobros.empresa_id',session('empresa_id'))
             ->where('cobros.fpago_id', '>', 1)
             ->whereDate('fecha','>=', $data['fecha_d'])
             ->whereDate('fecha','<=', $data['fecha_h'])
             ->get();

     }

    public function excel(Request $request){

        return Excel::download(new ApuntesBancoExport($request->data), 'apuban.xlsx');

    }

}
