<?php

use App\Fase;
use Illuminate\Database\Seeder;

class FasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $f = new Fase;
        $f->nombre = "Recepcionando";   //1
        $f->comven = "C";
        $f->color = "orange--text darken-4";
        $f->save();

        $f = new Fase;
        $f->nombre = "Pendiente Cierre";  //2
        $f->comven = "C";
        $f->color = "orange--text darken-4";
        $f->save();

        $f = new Fase;
        $f->nombre = "Sin Imprimir";   //3
        $f->color = "orange--text darken-4";
        $f->comven = "C";
        $f->save();

        $f = new Fase;
        $f->nombre = "Depósito";   //4
        $f->color = "blue--text darken-4";
        $f->comven = "C";
        $f->save();

        $f = new Fase;
        $f->nombre = "Recuperado";   //5
        $f->color = "green--text darken-4";
        $f->comven = "C";
        $f->save();

        $f = new Fase;
        $f->nombre = "Liquidado-Parcial";  //6
        $f->color = "red--text darken-4";
        $f->comven = "C";
        $f->save();

        $f = new Fase;
        $f->nombre = "Liquidado";   //7
        $f->color = "red--text darken-4";
        $f->comven = "C";
        $f->save();


        $f = new Fase;
        $f->id =   10;
        $f->nombre = "Por Cobrar";
        $f->color = "orange--text darken-4";
        $f->comven = "V";
        $f->save();
        $f = new Fase;
        $f->id =   11;
        $f->nombre = "Vendido";
        $f->color = "green--text darken-4";
        $f->comven = "V";
        $f->save();
        $f = new Fase;
        $f->id =   12;
        $f->nombre = "Abonado";
        $f->color = "red--text darken-4";
        $f->comven = "V";
        $f->save();
        $f = new Fase;
        $f->id =   13;
        $f->nombre = "Abono";
        $f->color = "red--text darken-4";
        $f->comven = "V";
        $f->save();
        $f = new Fase;
        $f->id =   14;
        $f->nombre = "Cancelado";
        $f->color = "red--text darken-4";
        $f->comven = "V";
        $f->save();
       // $f = new Fase;
        // $f->id =   20;
        // $f->nombre = "Vendido";
        // $f->color = "green--text darken-4";
        // $f->comven = "V";
        // $f->save();
    }
}
