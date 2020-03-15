<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class KlRjImportaProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $referencias = '"RE53369","RE58617"';

        // leo de kilates, origen
        $productos = DB::connection('db2')->select('select * from klt_productos WHERE referencia IN ('.$referencias.')');
        $existen = $creados = 0 ;

        foreach ($productos as $producto){

            \Log::info($producto->id);
            $pro_rj = DB::table('productos')->where('referencia', $producto->referencia);

            if ($pro_rj->exists()){
                $existen++;
                continue;
            }

            $pro_rj2 = $pro_rj->first();

            $this->crearProducto($producto);
            $creados++;

        }


        \Log::info("Existen: ".$existen);
        \Log::info("Creados: ".$creados);
    }

    private function crearProducto($producto_kil){

        // foreach ($producto_kil as $d){

            $data = collect($producto_kil)->toArray();

            //$data['id']=null;
            $data['empresa_id']=4;
            $data['destino_empresa_id']=4;
            $data['created_at']=Carbon::now();
            $data['updated_at']=Carbon::now();
            $data['username']='recupera';

            \Log::info($data);


        return DB::table('productos')->insertGetId($data);

    }
}
