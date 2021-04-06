<?php

namespace App\Traits;

use App\Producto;
use Carbon\Carbon;
use App\Existencia;
use Illuminate\Support\Facades\DB;


trait CalcularExistenciasTrait {


    public function valorDepositos($fecha){

        //\Log::info('CalcularExistenciasTrait'.$fecha);

        DB::table('existencias')->whereIn('detalle_id', [1,2])->where('fecha', $fecha)->delete();

        $select = DB::getTablePrefix().'compras.empresa_id AS empresa_id, '.DB::getTablePrefix().'compras.tipo_id AS tipo_id, SUM('.DB::getTablePrefix().'comlines.importe) AS importe';

        $data = DB::table('compras')
             ->select(DB::raw($select))
             ->join('empresas','empresas.id','=','compras.empresa_id')
             ->join('comlines','compras.id','=','comlines.compra_id')
             ->whereNull('empresas.deleted_at')
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
                'username'   => session('username') > '' ? session('usernanme') : 'sys',
                'created_at' => $dt,
                'updated_at' => $dt,
            ];

        }

        DB::table('existencias')->insert($existencias);


     }

    public function valorInventario($fecha){

        DB::table('existencias')->where('detalle_id', 3)->where('fecha', $fecha)->delete();

        $productos = DB::table('productos')
                        ->select('empresa_id','id','stock', 'precio_coste')
                        ->whereIn('estado_id', [1,2,3])
                        ->where('created_at', '<=', $fecha)
                        ->whereNull('deleted_at')
                        ->orderBy('empresa_id')
                        ->get();

        $valor_stock = 0;
        $empresa_id_ant = 0;
        foreach ($productos as $producto){

            if ($empresa_id_ant == 0){
                $empresa_id_ant = $producto->empresa_id;
            }
            if ($empresa_id_ant <> $producto->empresa_id){

                $this->crearStock($empresa_id_ant, $valor_stock, $fecha);

                $empresa_id_ant = $producto->empresa_id;
                $valor_stock = 0;
            }

            if ($producto->stock == 1){
                $valor_stock += ($producto->precio_coste);
            }else{
                $stock = Producto::getStockReal($producto->id);
                $valor_stock += ($producto->precio_coste * $stock);
            }

        }

        $this->crearStock($empresa_id_ant, $valor_stock, $fecha);

    }

    private function crearStock($empresa_id, $valor_stock, $fecha){

        $data['id']         = null;
        $data['detalle_id'] = 3;
        $data['empresa_id'] = $empresa_id;
        $data['fecha']      = $fecha;
        $data['importe']    = $valor_stock;
        $data['username']   = session('username') > '' ? session('usernanme') : 'sys';

        Existencia::create($data);
    }


}
