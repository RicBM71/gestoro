<?php

namespace App\Http\Controllers\Utilidades;

use App\Compra;
use App\Deposito;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AmpliarMasivoController extends Controller
{
    public function submit(Request $request)
    {

        if (!esRoot()){
            return abort(403, ' NO tiene permiso de acceso - root');
        }

        $data = $request->validate([
            'dias'     => ['required','integer'],
            'fecha_h'  => ['required','date'],
        ]);

        $depo = Deposito::where('concepto_id',4)
                          ->whereDate('fecha', '2020-05-01')                          
                          ->orderBy('compra_id')
                          ->get();

        $registros = 0;
        $compra_ant = -1;
        foreach ($depo as $row){
            if ($compra_ant != $row->compra_id){
                $compra_ant = $row->compra_id;
                $c=1;
            }
            if ($c > 1){

                $compra = Compra::findOrFail($row->compra_id);
                $fecha = Carbon::parse($compra->fecha_renovacion);

                $reg['fecha_renovacion'] = $fecha->subDays(49);
                $reg['username'] = 'Gerencia';
    
                $compra->update($reg);

                $row->delete();
                $registros++;
            }
            $c++;

        }

        // $compras = Compra::with(['cliente','grupo','tipo','fase'])
        //             ->tipo(1)
        //             ->fase(4)
        //             ->get();

        // $registros = 0;
        // $depositos = array();
        // foreach ($compras as $compra){

        //     if ($compra->retraso <= 0)
        //         continue;

        //     $fecha = Carbon::parse($compra->fecha_renovacion);

        //     $reg['fecha_renovacion'] = $fecha->addDays($data['dias']);
        //     $reg['username'] = 'Gerencia';

        //     $compra->update($reg);

        //     $depositos[] = array(
        //         'empresa_id' => session()->get('empresa')->id,
        //         'concepto_id'=> 4,
        //         'cliente_id'=> $compra->cliente_id,
        //         'compra_id' => $compra->id,
        //         'fecha'     => $data['fecha_h'],
        //         'importe'   => 0,
        //         'dias'      => $data['dias'],
        //         'notas'     => 'Suspensión x Estado de Alarma',
        //         'username'  => 'Gerencia',
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now()
        //     );

        //     $registros++;

        // }

        // DB::table('depositos')->insert($depositos);

        if (request()->wantsJson())
            return [
                'registros' => $registros
            ];


    }
}
