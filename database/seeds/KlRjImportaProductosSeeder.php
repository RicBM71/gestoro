<?php

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

        $referencias = array('KL53836','BM59347');

        // leo de kilates, origen
        $productos = DB::connection('db2')->select('select * from klt_productos WHERE referencia = ?',['BM59349']);

        foreach ($productos as $producto){

            $pro_rj = DB::table('productos')->where('referencia', $producto->referencia);

            if ($pro_rj->exists()){
                continue;
            }

            $pro_rj2 = $pro_rj->first();

            $this->crearProducto($producto);

        }
    }

    private function crearProducto($producto_kil){

        // foreach ($producto_kil as $d){
             \Log::info(collect($producto_kil));

        $data = collect($producto_kil)->toArray();

        $data['id']=null;
        $data['empresa_id']=3;
        $data['destino_empresa_id']=3;

        return DB::table('productos')->insertGetId($data);

    }
}
