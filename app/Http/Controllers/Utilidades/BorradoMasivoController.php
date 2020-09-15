<?php

namespace App\Http\Controllers\Utilidades;

use App\Caja;
use App\Hcaja;
use App\Compra;
use App\Hcompra;
use App\Hcomline;
use App\Hdeposito;
use Carbon\Carbon;
use App\Scopes\EmpresaScope;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BorradoMasivoController extends Controller
{
    public function index(){

        $fecha_d = Carbon::today();
        $fecha_h = Carbon::today();

        $fecha_h->endOfQuarter()->subQuarter();

        $fecha_d->subQuarter()->startOfQuarter();
        //$fecha_h = $dt->format('Y-m-d');

        return [
            'fecha_d' => $fecha_d->format('Y-m-d'),
            'fecha_h' => $fecha_h->format('Y-m-d')
        ];
    }

    public function ampliaciones(Request $request)
    {

        return abort(403, ' DESHABILITADO!! ');


        if (!esAdmin()){
            return abort(403, ' NO tiene permiso de acceso - admin');
        }


        $data=$request->validate([
            'fecha_d'     => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'     => ['required','date'],
        ]);

        $compras = Compra::withOutGlobalScope(EmpresaScope::class)
                            ->where('updated_at','>=',$data['fecha_d'])
                            ->where('updated_at','<=',$data['fecha_h'])
                            ->where('tipo_id', 1)
                            ->where('fase_id', 5)
                            ->get();

        $i=0;
        foreach ($compras as $compra){
            $i++;
            //$ret = DB::affectingStatement('DELETE FROM '.DB::getTablePrefix().'depositos WHERE compra_id = ? AND concepto_id = 4',[$compra->id]);
            DB::table('depositos')
                    ->where('compra_id', $compra->id)
                    ->where('concepto_id', 4)
                    ->delete();
        }

        if (request()->wantsJson())
            return [
                'registros' => $i
            ];


    }

    public function caja(Request $request)
    {


        if (!esAdmin()){
            return abort(403, ' NO tiene permiso de acceso - admin');
        }


        $data=$request->validate([
            'fecha_d'     => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'     => ['required','date'],
        ]);

        $saldo = Caja::saldoByEmpresa(session('empresa_id'), $data['fecha_h']);

        // histórico
        $ret = DB::affectingStatement('DELETE FROM '.DB::getTablePrefix().'hcajas WHERE empresa_id = ? AND fecha >= ? AND fecha <= ?',
                [
                    session('empresa_id'),
                    $data['fecha_d'],
                    $data['fecha_h'],
                ]);

        $ret = DB::affectingStatement('DELETE FROM '.DB::getTablePrefix().'cajas WHERE empresa_id = ? AND fecha >= ? AND fecha <= ?',
                [
                    session('empresa_id'),
                    $data['fecha_d'],
                    $data['fecha_h'],
                ]);

        if ($ret > 0){
            $this->apertura($data['fecha_h'], $saldo);
        }

        if (request()->wantsJson())
            return [
                'registros' => $ret
            ];


    }



    public function purgar(Request $request)
    {

        if (!esAdmin()){
            return abort(403, ' NO tiene permiso de acceso - admin');
        }

        Hcaja::truncate();
        Hcomline::truncate();
        Hdeposito::truncate();
        Hcompra::truncate();

        return response('Purgado Ok',200);

    }

    private function apertura($fecha, $saldo){

            if ($saldo == 0){
                return;
            }

            $data = [
                'empresa_id'=> session('empresa_id'),
                'nombre'    => 'Apertura',
                'importe'   => $saldo,
                'fecha'     => $fecha,
                'dh'        => 'H',
                'manual'    => 'R',
                'username'  => session('username')
            ];

            Caja::create($data);
    }

}
