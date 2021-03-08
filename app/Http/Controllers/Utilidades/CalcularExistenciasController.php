<?php

namespace App\Http\Controllers\Utilidades;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CalcularExistenciasController extends Controller
{

    public function submit(){

        $this->depositos('2021-01-01');
        $this->inventario('2021-01-01');

        dd(Existencia::all());

    }

    private function depositos($fecha){


        $select = DB::getTablePrefix().'compras.empresa_id AS empresa_id, '.DB::getTablePrefix().'compras.tipo_id AS tipo_id, SUM('.DB::getTablePrefix().'comlines.importe) AS importe';

        $data = DB::table('compras')
             ->select(DB::raw($select))
             ->join('comlines','compras.id','=','comlines.compra_id')
             ->where('compras.empresa_id', session('empresa')->id)
             ->whereIn('compras.fase_id', [4,6])
             ->whereDate('fecha_compra','<=', $fecha)
             ->whereNull('fecha_liquidado')
             ->groupBy(DB::raw('empresa_id, tipo_id'))
             ->get();

        $dt = Carbon::now();

        $existencias=array();
        foreach ($data as $row){

            $existencias[]=[
                'empresa_id' => $row->empresa_id,
                'fecha'      => $fecha,
                'detalle_id' => $row->tipo_id,
                'importe'    => $row->importe,
                'username'   => session('username'),
                'created_at' => $dt,
                'updated_at' => $dt,
            ];

        }

        DB::table('existencias')->insert($existencias);


     }

     private function inventario($fecha){


        $select = 'empresa_id, SUM('.DB::getTablePrefix().'productos.precio_coste) AS importe';

        $data = DB::table('productos')
            ->select(DB::raw($select))
            ->whereIn('estado_id',[1,2,3])
            ->whereDate('productos.created_at','<=', $fecha)
            ->whereNull('productos.deleted_at')
            ->groupBy(DB::raw('empresa_id '))
            ->get();


        $dt = Carbon::now();

        $existencias=array();
        foreach ($data as $row){

            $existencias[]=[
                'empresa_id' => $row->empresa_id,
                'fecha'      => $fecha,
                'detalle_id' => 3,
                'importe'    => $row->importe,
                'username'   => session('username'),
                'created_at' => $dt,
                'updated_at' => $dt,
            ];

        }

        DB::table('existencias')->insert($existencias);
    }


}
