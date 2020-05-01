<?php

use App\Caja;
use Illuminate\Database\Seeder;

class KltCajaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Caja::truncate();

        $reg = DB::connection('quilates')
            ->select('select * from cajasmov WHERE YEAR(fecha)=2020');


        $i=0;
        $emp_ant = $tie_ant = -1;
        foreach ($reg as $row){

            if ($emp_ant <> $row->empresa || $tie_ant <> $row->tienda){
                $r = DB::connection('quilates')->select('select * from crulara WHERE empresa ='.$row->empresa.' AND tienda='.$row->tienda);
                foreach ($r as $cruce){
                }
                $emp_ant = $row->empresa;
                $tie_ant = $row->tienda;
            }

            $concepto = html_entity_decode($row->concepto);

            if (strpos($concepto, 'Depósito')!==false){
                $apunte_id = 1;
                $manual = "N";
            }
            elseif (strpos($concepto, 'Ampliación')!==false){
                $manual = "N";
                $apunte_id = 4;
            }
            elseif (strpos($concepto, 'A cuenta')!==false){
                $manual = "N";
                $apunte_id = 6;
            }
            elseif (strpos($concepto, 'Recuperado')!==false){
                $manual = "N";
                $apunte_id = 8;
            }
            elseif (strpos($concepto, 'Comprado')!==false){
                $manual = "N";
                $apunte_id = 11;
            }
            elseif (strpos($concepto, 'Venta')!==false){
                $manual = "N";
                $apunte_id = 21;
            }
            elseif (strpos($concepto, 'Cierre')!==false){
                $manual = "S";
                $apunte_id = 30;
            }
            else{
                $manual = $row->manual == 'S' ? 'S' : 'N';
                $apunte_id = null;
            }

            $data[]=array(

                'fecha'=>$row->fecha,
                'dh'=>$row->dh,
                'empresa_id'=> $cruce->emp_alb,
                'nombre' => $concepto,
                'importe'=> ($row->importe),
                'manual'=>  $manual,
                'apunte_id' => null,
                'username' => $row->sysusr,
                'created_at' => $row->sysfum.' '.$row->syshum,
                'updated_at' => $row->sysfum.' '.$row->syshum,
            );

            if ($i % 3000 == 0){
                DB::table('cajas')->insert($data);
                $data=array();
            }
        }

        DB::table('cajas')->insert($data);

        // return;

        // $reg = DB::connection('quilates')
        //     ->select('select * from saldos WHERE YEAR(fecha)=2020');

        // $data=array();
        // $emp_ant = $tie_ant = -1;
        // foreach ($reg as $row){

        //     if ($emp_ant <> $row->empresa || $tie_ant <> $row->tienda){
        //         $r = DB::connection('quilates')->select('select * from crulara WHERE empresa ='.$row->empresa.' AND tienda='.$row->tienda);
        //         foreach ($r as $cruce){
        //         }
        //         $emp_ant = $row->empresa;
        //         $tie_ant = $row->tienda;
        //     }

        //     $concepto = "REGULARIZACIÓN";

        //     $data[]=array(

        //         'fecha'=>$row->fecha,
        //         'dh'=> 'H',
        //         'empresa_id'=> $cruce->emp_alb,
        //         'nombre' => $concepto,
        //         'importe'=> ($row->saldo),
        //         'manual'=>  'R',
        //         'apunte_id' => null,
        //         'username' => $row->sysusr,
        //         'created_at' => $row->sysfum.' '.$row->syshum,
        //         'updated_at' => $row->sysfum.' '.$row->syshum,
        //     );

        // }

        // DB::table('cajas')->insert($data);


    }
}
