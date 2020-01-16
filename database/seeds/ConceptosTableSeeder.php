<?php

use App\Apunte;
use App\Concepto;
use Illuminate\Database\Seeder;

class ConceptosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Concepto::truncate();


        $con = new Concepto;
        $con->comven="C";
        $con->color = "blue--text darken-4";
        $con->nombre='Depósito Efectivo'; //1
        $con->signo = -1;
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->nombre='Depósito Transferencia'; //2
        $con->color = "blue--text darken-4";
        $con->signo = -1;
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->nombre='Depósito Talón'; //3
        $con->color = "blue--text darken-4";
        $con->signo = -1;
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->color = "indigo--text darken-4";
        $con->nombre='Ampliación Efectivo'; //4
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->color = "indigo--text darken-4";
        $con->nombre='Ampliación Banco'; //5
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->color = "orange--text darken-4";
        $con->nombre='A cuenta Efectivo'; //6
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->color = "orange--text darken-4";
        $con->nombre='A cuenta Banco'; //7
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->color = "green--text darken-4";
        $con->nombre='Recuperado Efectivo'; //8
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->color = "green--text darken-4";
        $con->nombre='Recuperado Banco'; //9
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->nombre='Liquidado'; //10
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->nombre='Comprado Efectivo'; //11
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->nombre='Comprado Banco'; //12
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->nombre='Ampliación Capital Efectivo'; //13
        $con->color = "purple--text darken-4";
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->nombre='Ampliación Capital Banco'; //14
        $con->color = "purple--text darken-4";
        $con->save();

        $con = new Concepto;
        $con->id=30;
        $con->comven="C";
        $con->nombre='Cierre';
        $con->color = "green--text darken-4";
        $con->save();




        // $con = new Concepto;
        // $con->comven="V";
        // $con->nombre='Efectivo'; //13
        // $con->save();

        // $con = new Concepto;
        // $con->comven="V";
        // $con->nombre='T/Crédito'; //14
        // $con->save();

        // $con = new Concepto;
        // $con->comven="V";
        // $con->nombre='Transferencia'; //15
        // $con->save();



    }
}
