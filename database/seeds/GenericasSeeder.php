<?php

use App\Iva;
use App\Tipo;
use App\Clase;
use App\Fpago;
use App\Grupo;
use App\Libro;
use App\Apunte;
use App\Estado;
use App\Motivo;
use App\Taller;
use App\Almacen;
use App\Empresa;
use App\Quilate;
use App\Etiqueta;
use App\Rfid;
use App\Parametro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;


class GenericasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Empresa::truncate();
        // Almacen::truncate();
        Grupo::truncate();
        Clase::truncate();
        Libro::truncate();
        FPago::truncate();
        Tipo::truncate();
        Iva::truncate();
        Estado::truncate();
        Quilate::truncate();
        Motivo::truncate();
        Taller::truncate();


        DB::table('empresa_user')->truncate();

        /*
        $emp = new Empresa;
        $emp->nombre = "Demo";
        $emp->razon = "Demo";
        $emp->cif="B82848417";
        $emp->titulo = "Demo T";
       // $emp->logo = "logo.jpg";
        $emp->certificado = "";
        $emp->passwd_cer="";
        $emp->almacen_id = 1;
        $emp->flags='11111000000000000000';
        $emp->save();


        $alm = new Almacen;
        $alm->empresa_id=1;
        $alm->nombre = "Almacén 1";
        $alm->save();

        DB::table('empresa_user')->insert(
            ['empresa_id' => 1, 'user_id' => '1'],
            ['empresa_id' => 1, 'user_id' => '2']
        );
        */
        $grupo = new Grupo;
        $grupo->nombre = "Metal";
        $grupo->leyenda = "metales preciosos";
        $grupo->save();

        $grupo = new Grupo;
        $grupo->nombre = "Usados";
        $grupo->leyenda = "objetos usados";
        $grupo->save();

        $clase = new Clase;

        $clase->nombre = "Oro";
        $clase->grupo_id = 1;
        $clase->peso = true;
        $clase->quilates = true;

        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Plata";
        $clase->grupo_id = 1;
        $clase->peso = true;
        $clase->quilates = false;
        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Platino";
        $clase->grupo_id = 1;
        $clase->peso = true;
        $clase->quilates = false;
        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Piedras Preciosas";
        $clase->grupo_id = 1;
        $clase->peso = true;
        $clase->quilates = true;
        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Otros";
        $clase->grupo_id = 1;
        $clase->peso = false;
        $clase->quilates = false;
        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Brillante";
        $clase->grupo_id = 2;
        $clase->peso = false;
        $clase->quilates = false;
        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Reloj";
        $clase->grupo_id = 2;
        $clase->peso = false;
        $clase->quilates = false;
        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Otros";
        $clase->grupo_id = 2;
        $clase->peso = false;
        $clase->quilates = false;
        $clase->save();

        $libro = new Libro;
        $libro->nombre = "Metales";
        $libro->empresa_id = 1;
        $libro->grupo_id = 1;
        $libro->serie_fac = "RM";
        $libro->serie_com = "M";

        $libro->ejercicio = 2020;
        $libro->ult_compra = 0;
        $libro->ult_factura= 0;
        $libro->interes = 10;
        $libro->semdia_bloqueo = "3/1";
        $libro->grabaciones = true;
        $libro->dias_custodia= 30;

        $libro->save();

        $libro = new Libro;
        $libro->nombre = "Usados";
        $libro->empresa_id = 1;
        $libro->grupo_id = 2;
        $libro->serie_fac = "RU";
        $libro->serie_com = "U";
        $libro->ejercicio = 2020;
        $libro->ult_compra = 0;
        $libro->ult_factura= 0;
        $libro->interes = 10;
        $libro->grabaciones = false;
        $libro->semdia_bloqueo = "1/1";
        $libro->dias_custodia = 30;

        $libro->save();

        $fp = new Fpago;
        $fp->id =1 ;
        $fp->nombre = "Efectivo";
        $fp->save();

        $fp = new Fpago;
        $fp->id =2 ;
        $fp->nombre = "Transferencia";
        $fp->save();

        $fp = new Fpago;
        $fp->id =3 ;
        $fp->nombre = "Talón";
        $fp->save();

        $fp = new Fpago;
        $fp->id =4 ;
        $fp->nombre = "Tarjeta";
        $fp->save();


        $tipo = new Tipo;
        $tipo->nombre = "RECOMPRA";
        $tipo->abreviatura="R";
        $tipo->save();


        $tipo = new Tipo;
        $tipo->nombre = "COMPRA";
        $tipo->abreviatura="C";
        $tipo->save();

        $tipo = new Tipo;
        $tipo->nombre = "ALBARAN REBU";
        $tipo->abreviatura="R";
        $tipo->save();

        $tipo = new Tipo;
        $tipo->nombre = "ALBARAN";
        $tipo->abreviatura="V";
        $tipo->save();

        $tipo = new Tipo;
        $tipo->nombre = "TALLER";
        $tipo->abreviatura="T";
        $tipo->save();



        $par = new Parametro;
        $par->pie_rebu1 = "Dispone de un plazo máximo de 15 días para su devolución. No se reembolsará dinero.";
        $par->retencion = 0;
        //$par->img1 = 'hero.jpeg';
        $par->save(); // con valores defecto migración de momento.

        $reg = new Iva;
        $reg->nombre = "General";
        $reg->importe = 21;
        $reg->rebu = false;
        $reg->save();

        $reg = new Iva;
        $reg->nombre = "REBU";
        $reg->importe = 21;
        $reg->leyenda="* IVA: Sujeto a régimen especial de bienes usados";
        $reg->rebu = true;
        $reg->save();

        $reg = new Iva;
        $reg->nombre = "Exento Oro";
        $reg->importe = 0;
        $reg->leyenda="IVA NO APLICABLE SEGUN LEY 55/1999. BOE 30.12.1999";
        $reg->rebu = false;
        $reg->save();

        $reg = new Iva;
        $reg->nombre = "Exento Metales";
        $reg->importe = 0;
        $reg->leyenda="IVA NO APLICABLE SEGÚN LEY 28/2014 y R.D.073/2014  artículo 84.Uno.2º del IVA";
        $reg->rebu = false;
        $reg->save();

        $reg = new Estado;
        $reg->nombre = "Inventario ";
        $reg->color="blue--text";
        $reg->save();

        $reg = new Estado;
        $reg->nombre = "En Venta ";
        $reg->color="green--text";
        $reg->save();

        $reg = new Estado;
        $reg->color="orange--text";
        $reg->nombre = "Reservado ";
        $reg->save();

        $reg = new Estado;
        $reg->color="red--text";
        $reg->nombre = "Vendido ";
        $reg->save();

        $reg = new Estado;
        $reg->color="black--text";
        $reg->nombre = "Genérico ";
        $reg->save();

        $reg = new Estado;
        $reg->color="black--text";
        $reg->nombre = "Gastos Envío";
        $reg->activo = false;
        $reg->save();

        $quilates_oro=array('9','10','14','15','16','17','18','19','20','21','22','23','24');
        foreach ($quilates_oro as $row){

            $reg = new Quilate();
            $reg->id = $row;
            $reg->nombre = $row."K";
            $reg->save();

        }

        $et = new Etiqueta();
        $et->nombre = "No generar";
        $et->save();

        $et = new Etiqueta();
        $et->nombre = "Imprimir sin PVP";
        $et->save();
        $et = new Etiqueta();
        $et->nombre = "Imprimir con PVP";
        $et->save();

        $et = new Etiqueta();
        $et->nombre = "Devolución";
        $et->save();

        $et = new Etiqueta();
        $et->nombre = "Impresa";
        $et->save();
        // $et = new Etiqueta();
        // $et->nombre = "Impresa con PVP";
        // $et->save();

        $mot = new Motivo();
        $mot->nombre = "Falta de información importante en el documento";
        $mot->save();

        $mot = new Motivo();
        $mot->nombre = "Error o ausencia en los datos profesionales";
        $mot->save();

        $mot = new Motivo();
        $mot->nombre = "IVA erróneo";
        $mot->save();

        $mot = new Motivo();
        $mot->nombre = "Importes erroneos";
        $mot->save();

        $mot = new Motivo();
        $mot->nombre = "Devolución de productos";
        $mot->save();

        $mot = new Motivo();
        $mot->nombre = "Otros";
        $mot->save();

        $taller = new Taller;
        $taller->empresa_id = 1;
        $taller->nombre = "Taller Demo";
        $taller->razon = "Taller Demo";
        $taller->save();


        $rec = new Rfid;
        $rec->id = 1;
        $rec->nombre = "OK";
        $rec->save();

        $rec = new Rfid;
        $rec->id = 2;
        $rec->nombre = "Difiere ubicación de tienda";
        $rec->save();

        $rec = new Rfid;
        $rec->id = 3;
        $rec->nombre = "Referencia Perdida";
        $rec->save();

        $rec = new Rfid;
        $rec->id = 4;
        $rec->nombre = "Referencia marcada como baja y en recuento";
        $rec->save();

        $rec = new Rfid;
        $rec->id = 5;
        $rec->nombre = "Referencia VENDIDA";
        $rec->save();

        $rec = new Rfid;
        $rec->id = 6;
        $rec->nombre = "Referencia RESERVADA";
        $rec->save();

        $rec = new Rfid;
        $rec->id = 12;
        $rec->nombre = "LOCALIZADA-Otra ubicacion";
        $rec->save();

        $rec = new Rfid;
        $rec->id = 13;
        $rec->nombre = "LOCALIZADA-Referencia perdida";
        $rec->save();

        $rec = new Rfid;
        $rec->id = 14;
        $rec->nombre = "LOCALIZADA-Baja";
        $rec->save();

        $rec = new Rfid;
        $rec->id = 15;
        $rec->nombre = "LOCALIZADA-Vendida";
        $rec->save();

        $rec = new Rfid;
        $rec->id = 16;
        $rec->nombre = "LOCALIZADA-Reservada";
        $rec->save();



        // $apunte = new Apunte;
        // $apunte->nombre = "Origen Compra";
        // $apunte->fijo = true;
        // $apunte->save();

        // $apunte = new Apunte;
        // $apunte->nombre = "Origen Venta";
        // $apunte->fijo = true;
        // $apunte->save();

        // $apunte = new Apunte;
        // $apunte->nombre = "Cierre";
        // $apunte->fijo = true;
        // $apunte->save();

        // $apunte = new Apunte;
        // $apunte->nombre = "Compra";
        // $apunte->fijo = true;
        // $apunte->save();


    }
}
