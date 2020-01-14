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
        $con->save();

        $apunte = new Apunte;  //1
        $apunte->id=1;
        $apunte->nombre = $con->nombre;
        $apunte->color = $con->color;
        $apunte->save();


        $con = new Concepto;
        $con->comven="C";
        $con->nombre='Depósito Transferencia'; //2
        $con->color = "blue--text darken-4";
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->nombre='Depósito Talón'; //3
        $con->color = "blue--text darken-4";
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->color = "indigo--text darken-4";
        $con->nombre='Ampliación Efectivo'; //4
        $con->save();

        $apunte = new Apunte;  // 2
        $apunte->id=4;
        $apunte->nombre = $con->nombre;
        $apunte->color = $con->color;
        $apunte->save();

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

        $apunte = new Apunte;
        $apunte->id=6;
        $apunte->nombre = $con->nombre;
        $apunte->color = $con->color;
        $apunte->save();

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

        $apunte = new Apunte; //
        $apunte->id=8;
        $apunte->nombre = $con->nombre;
        $apunte->color = $con->color;
        $apunte->save();

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

        $apunte = new Apunte; //
        $apunte->id=11;
        $apunte->nombre = $con->nombre;
        $apunte->color = $con->color;
        $apunte->save();

        $con = new Concepto;
        $con->comven="C";
        $con->nombre='Comprado Banco'; //12
        $con->save();

        $con = new Concepto;
        $con->comven="C";
        $con->nombre='Ampliación Capital Efectivo'; //13
        $con->color = "purple--text darken-4";
        $con->save();

        $apunte = new Apunte; //
        $apunte->id=13;
        $apunte->nombre = $con->nombre;
        $apunte->color = $con->color;
        $apunte->save();

        $con = new Concepto;
        $con->comven="C";
        $con->nombre='Ampliación Capital Banco'; //14
        $con->color = "purple--text darken-4";
        $con->save();



        $apunte = new Apunte; //
        $apunte->id=21;
        $apunte->nombre = "A cuenta";
        $apunte->color = 'blue--text darken 4';
        $apunte->save();

        $apunte = new Apunte; //
        $apunte->id=30;
        $apunte->nombre = "Cierre";
        $apunte->color = 'grey--text darken 4';
        $apunte->save();



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
