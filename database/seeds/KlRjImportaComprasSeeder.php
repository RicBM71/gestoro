<?php

use App\Compra;
use App\Empresa;
use App\Scopes\EmpresaScope;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KlRjImportaComprasSeeder extends Seeder
{
    protected $empresa_id;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->empresa_id = 5;

        \Log::info(Empresa::find($this->empresa_id));

        session([
            'empresa_id'       => $this->empresa_id,
            'empresa'          => Empresa::find($this->empresa_id)]);

        $klt_compras = array('6047-17');

        foreach ($klt_compras as $row){


            $data = explode("-", $row);


            $ejercicio  = $data[1] + 2000;
            $albaran    = $data[0];

            $compra_kilates = DB::connection('db2')->select('select * from klt_compras WHERE empresa_id = 1 AND year(fecha_compra) = ? AND albaran = ?',[$ejercicio, $albaran]);

            //\Log::info(collect($compra_kilates[0]));

            //break;


            try {
                //code...
                $compra_rejoya = Compra::whereYear('fecha_compra', $ejercicio)
                                        ->where('albaran', $albaran)
                                        ->firstOrFail();
                continue;

            } catch (\Exception $e) {
             //   \Log::info($compra_kilates[0]);
                $this->crearCompra($compra_kilates[0]);
            }
        }

        // $compras = DB::connection('db2')->select('select * from klt_compras WHERE empresa_id = 1');
        // foreach ($compras as $compra){

        //     try {
        //         //code...
        //         $compra_recupera = Compra::withOutGlobalScope(EmpresaScope::class)->findOrFail($compra->id);
        //         continue;

        //     } catch (\Exception $e) {
        //         \Log::info($compra->id);
        //         $this->crearCompra($compra);
        //     }
        // }
    }

    private function crearCompra($compra_kilates){


        $data = collect($compra_kilates);

        \Log::info($data);

        $cliente_id = $this->checkCliente($compra_kilates->cliente_id);

        $data['fase_id'] = 4;
        $data = $data->toArray();
        $data['empresa_id'] = 5;
        $data['id']=null;
        $data['cliente_id']=$cliente_id;
     //   $data['username']='Traspaso';
       // \Log::info($data);

        $nueva_compra = DB::table('compras')->insertGetId($data);

        $this->crearLineas($compra_kilates->id, $nueva_compra);

    }

    private function crearLineas($compra_id, $nueva_compra){

        $lineas = DB::connection('db2')->select('select * from klt_comlines WHERE compra_id = ?',[$compra_id]);
        foreach ($lineas as $linea){

            $l = collect($linea)->toArray();

            $l['empresa_id'] = $this->empresa_id;
            $l['compra_id'] = $nueva_compra;
            $l['id']=null;
            $l['fecha_liquidado'] = null;
            DB::table('comlines')->insertGetId($l);
        }

        $depo = DB::connection('db2')->select('select * from klt_depositos WHERE compra_id = ?',[$compra_id]);
        foreach ($depo as $linea){
            $l = collect($linea)->toArray();
            $l['empresa_id'] = $this->empresa_id;
            $l['compra_id'] = $nueva_compra;
            $l['id']=null;
            if ($l['username'] == 'Gerencia')
                $l['notas'] = null;
            DB::table('depositos')->insertGetId($l);
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

        //  \Log::info("Creados ".$creados);
        //  \Log::info("Existe ".$existen);
    }

    private function crearCliente($cliente_kil){

        $data = collect($cliente_kil);
           \Log::info($data);

        $data = $data->toArray();

        //$data = $data[0];
        $data['id']=null;


        return DB::table('clientes')->insertGetId($data);

    }
}
