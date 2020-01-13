<?php

use App\Cliente;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ClientesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cliente::truncate();
        $faker = Faker::create();


        $cli = new Cliente;

        for ($i=0; $i < 20 ; $i++) {

            $cli = new Cliente;

            $cli->empresa_id = $i <= 10 ? 1: 2;
            $cli->nombre = $faker->firstName;
            $cli->apellidos = $faker->lastName;
            $cli->razon = $cli->nombre.' '.$cli->apellidos;
            $cli->dni = "000".$i;
            $cli->tipodoc = "O";

            $cli->direccion = $faker->streetAddress;
            $cli->cpostal   =   $faker->randomNumber(5);
            $cli->poblacion = $faker->state;
            $cli->provincia=$faker->state;

            $cli->fecha_nacimiento=$faker->date();
            $cli->fecha_baja=null;
            $cli->nacpro    =$faker->state;
            $cli->nacpais   =$faker->country;
            $cli->fecha_dni=$faker->date();


            $cli->save();
        }

    }
}
