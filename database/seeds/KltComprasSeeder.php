<?php

use App\Libro;
use App\Compra;
use App\Comline;
use App\Empresa;
use App\Deposito;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KltComprasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $empresas = array(1,9,12,16);
        $e='1,9,12,16';
        $empresas = array(1);



        //insert into contadores SELECT DISTINCT 0,empresa,tienda,ejercicio, '1', 'S', '1', '1', '1', 'A', '1', '1', '1', 'sys','2019-01-01','00:00:00' from albaranes where ejercicio <= 2010

        Compra::truncate();

        $empresa_ant = $tienda_ant = -1;
        $ejercicio = 2019;
        //$contadores = DB::connection('quilates')->select('select * from contadores WHERE empresa in('.$e.') ORDER BY id');

        $contadores = DB::connection('quilates')->select('select * from crulara ORDER BY empresa');


        foreach ($contadores as $contador){


           // if ($empresa_ant <> $contador->empresa || $tienda_ant <> $contador->tienda){

                session(['empresa' => Empresa::find($contador->emp_com)]);

                //$libro = Libro::find($contador->id);

            //     $empresa_ant = $contador->empresa;
            //     $tienda_ant = $contador->tienda;
            // }

          //  \Log::info($contador->emp_com);
         // comentar if para cargar todas las empresas
         //   if (in_array($contador->emp_com, $empresas)){

                for ($ejercicio = 2008; $ejercicio <= 2020; $ejercicio++)
                    $this->crearCompras($contador, $ejercicio);
           // }

        }

        $parcial_liquidados = DB::select('SELECT id FROM klt_compras where fase_id = 4 and id in (select compra_id from klt_comlines where fecha_liquidado != "")');
        foreach ($parcial_liquidados as $c){

            DB::update('UPDATE klt_compras SET fase_id = 6 WHERE id='.$c->id);

        }


    }

    private function crearCompras($contador, $eje){

        //$bloqueo = "1/3"; // "1/1
        //$eje = 2000;

        $data=array();

        $reg = DB::connection('quilates')->select('select * from albaranes where empresa='.$contador->empresa.
                        ' and tienda = '.$contador->tienda.
                        ' and comven="C" and year(fechacomp) = '.$eje.' order by empresa,tienda,id');
        if ($reg == null)
            return;


        $i=0;
        foreach ($reg as $row){
            $i++;

            if ($contador->libro == "M"){
                $grupo_id = 1;
                $serie_com = 'M';
                $semdia_bloqueo="3/1";
            }else{
                $grupo_id = 2;
                $serie_com = 'U';
                $semdia_bloqueo="1/1";
            }



            $empresa_id = $contador->emp_com;

            if ($row->empresa == 14 && $row->ejercicio == 2020){
                if ($serie_com == 'M')
                    $serie_com = "A";
                else
                    $serie_com = "B";

            }


            // if ($empresa_id == 14 && $row->ejercicio == 2020 && $grupo_id == 1){
            //     $serie_com = 'X';
            // }
            // if ($empresa_id == 14 && $row->ejercicio == 2020 && $grupo_id == 2){
            //     $serie_com = 'Y';
            // }


            // if(in_array($row->empresa, [12,13,14,15,16])){

            // }
            // CREATE TEMPORARY TABLE xx
            // update klt_compras set fase_id = 6 where id in (select id from xx)

            if (strlen($row->notas) >= 191)
                $row->notas = substr($row->notas,0,190);

            $data[]=array(
                'id'        => $row->id,
                'empresa_id' => $empresa_id,
                'grupo_id'=> $grupo_id,
                'dias_custodia' => 30,
                'ejercicio' => $row->ejercicio,
                'serie_com' => $serie_com,
                'albaran' => $row->albaran,
                'cliente_id'=> $row->cliente,
                'tipo_id' => $row->tipo,
                'fecha_compra' => $row->fechacomp,
                'fecha_bloqueo'=> $this->Bloqueo($row->fechacomp, $semdia_bloqueo),
                'fecha_renovacion' => $row->fecharecu,
                'fecha_recogida' => $row->fecharecogida=="0000-00-00" ? null : $row->fecharecogida,
                'importe' => $row->importe,
                'importe_renovacion' => $row->impren,
                'importe_acuenta' => $row->impacuenta,
                'interes' => $row->interes,
                'fase_id' => $row->estado==6 ? 7: $row->estado,
                'factura' => $row->factura == 0 ? null : $row->factura,
                'fecha_factura' => $row->fechafac=="0000-00-00" ? null : $row->fechafac,
                'serie_fac' => $row->serie,
                'papeleta' => $row->papeleta==0 ? null : $row->papeleta,
                'notas' => $row->notas,
                'username' => $row->sysusr,
                'created_at' => $row->sysfum.' '.$row->syshum,
                'updated_at' => $row->sysfum.' '.$row->syshum,
            );

            if ($i % 1000 == 0){
                DB::table('compras')->insert($data);
                $data=array();
            }
        }

        DB::table('compras')->insert($data);
    }

    public function Bloqueo($fecha, $semdia){



        $s = explode("/", $semdia);
        // $fecha = date('Y-m-d', strtotime($fecha));
        //$f = Carbon::createFromFormat('Y-m-d h:i:s', $fecha);
        $f = Carbon::parse($fecha);

        $f->addWeek($s[0]);

        return $f->subDays($f->dayOfWeek - $s[1]);

    }

}
