<?php

namespace App\Http\Controllers\Utilidades;

use App\Compra;
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


        $compras = Compra::with(['cliente','grupo','tipo','fase'])
                    ->tipo(1)
                    ->fase(4)
                    ->get();

        $registros = 0;
        $depositos = array();
        foreach ($compras as $compra){

            if ($compra->retraso <= 0)
                continue;

            $fecha = Carbon::parse($compra->fecha_renovacion);

            $reg['fecha_renovacion'] = $fecha->addDays($data['dias']);
            $reg['username'] = 'Gerencia';

            $compra->update($reg);

            $depositos[] = array(
                'empresa_id' => session()->get('empresa')->id,
                'concepto_id'=> 4,
                'cliente_id'=> $compra->cliente_id,
                'compra_id' => $compra->id,
                'fecha'     => $data['fecha_h'],
                'importe'   => 0,
                'dias'      => $data['dias'],
                'notas'     => 'Suspensión x Estado de Alarma',
                'username'  => 'Gerencia',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );

            $registros++;

        }

        DB::table('depositos')->insert($depositos);

        if (request()->wantsJson())
            return [
                'registros' => $registros
            ];


    }
}
