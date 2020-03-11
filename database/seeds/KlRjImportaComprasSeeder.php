<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KlRjImportaComprasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        session('empresa_id', 2);

        $compras = DB::connection('db2')->select('select * from klt_albaranes WHERE empresa_id = 11');
        foreach ($compras as $compra){

            try {
                //code...
                $compra_quilates = Compra::findOrFail($compra->id);
                continue;

            } catch (\Exception $e) {
                $this->crearCompra($compra);
            }
        }
    }

    private function crearCompra($row){

        $this->checkCliente($row->cliente_id);

        $data = collect($row);
        $data = $data->toArray();
        $data = $data[0];

        DB::table('klt_compras')->insertGetId($data);

        $this->crearLineas($row->compra_id);

    }

    private function crearLineas(){

        $lineas = Comlines::where('compra_id')->get();
        foreach ($lineas as $linea){
            
        }

    }

    private function checkCliente($id){

         // leo de kilates, origen
         $clientes = DB::connection('db2')->select('select * from klt_clientes WHERE id = ?', [$id]);

         $creados = $existen = 0;
         foreach ($clientes as $cliente_kil) {

             $cliente = DB::table('clientes')->where('dni', $cliente_kil->dni);

             if ($cliente->exists()){
                 $existen++;
                 $cliente_id = $cliente->first()->id;
             }
             else{
                 $creados++;
                 $cliente_id = $this->crearCliente($cliente_kil);
             }


         }

        return $cliente_id;

         \Log::info("Creados ".$creados);
         \Log::info("Existe ".$existen);
    }

    private function crearCliente($cliente_kil){

        $data = collect($cliente_kil);

        $data = $data->toArray();

        $data = $data[0];
        $data['id']=null;

        \Log::info($data);

        return DB::table('clientes')->insertGetId($data);

    }
}
