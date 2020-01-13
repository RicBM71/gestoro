<?php

use App\Producto;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ProductosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Producto::truncate();
        $faker = Faker::create();


        $pro = new Producto;

        for ($i=0; $i < 20 ; $i++) {

            $pro = new Producto;

            $pro->empresa_id = $i <= 10 ? 1: 2;
            $pro->destino_empresa_id = $pro->empresa_id;
            $pro->almacen_id = $pro->empresa_id;
            $pro->clase_id = $faker->randomElement($array = array ('1','2','3'));
            $pro->nombre = $faker->freeEmailDomain;
            $pro->precio_coste =$faker->randomFloat(2,3,2300);
            $pro->precio_venta = $pro->precio_coste * $faker->randomDigit;
            $pro->referencia = "Ref.".$i;
            $pro->compra_id = $faker->randomDigit;
            $pro->iva_id = $faker->randomElement($array = array ('1','2','3'));

            $pro->save();
        }
    }
}
