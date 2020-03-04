<?php

use App\Contador;
use Illuminate\Database\Seeder;

class KltContadoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Contador::truncate();

        $reg = DB::connection('quilates')
        //->select('select * from contadores WHERE id=148');
        ->select('select * from contadores '.
                ' WHERE albaran > 1001 '.
                ' AND ejercicio = 2020 AND empresa <> 14'.
                ' ORDER BY contadores.id');


        foreach ($reg as $row){
            $c = DB::connection('quilates')->select('select * from crulara where empresa ='.$row->empresa.' AND tienda ='.$row->tienda);

            foreach ($c as $cruce){
                \Log::info($cruce->libro.' T:'.$row->tienda);
            }

            $data[]=array(
                'empresa_id'=> $cruce->empresa,
                'tipo_id'=> 3,
                'ejercicio'=>$row->ejercicio,
                'ult_albaran'=>$row->albaran - 1,
                'serie_albaran' => 'R',
                'ult_factura'=> $row->factura - 1,
                'serie_factura'=>$row->serie,
                'ult_factura_auto'=>$row->atfactura - 1,
                'serie_factura_auto'=> 'FA',
                'serie_factura_abono'=>'RR',
                'ult_factura_abono'=>0,
                'cerrado' => $row->ejercicio == 2020 ? false: true,
                'created_at' => $row->sysfum.' 00:00:00',
                'updated_at' => $row->sysfum.' '.$row->syshum,
                'username'=> $row->sysusr
            );

                // mete contador general si hay algo
            //if ($row->gefactura > 1)
                $data[]=array(
                    'empresa_id'=> $cruce->empresa,
                    'tipo_id'=> 4,
                    'ejercicio'=>$row->ejercicio,
                    'ult_albaran'=>0,
                    'serie_albaran'=>'A',
                    'ult_factura'=> 0,
                    'serie_factura'=>'G',
                    'ult_factura_auto'=>0,
                    'serie_factura_auto'=>'GA',
                    'serie_factura_abono'=>'GR',
                    'ult_factura_abono'=>0,
                    'cerrado' => $row->ejercicio == 2020 ? false: true,
                    'created_at' => $row->sysfum.' 00:00:00',
                    'updated_at' => $row->sysfum.' '.$row->syshum,
                    'username'=> $row->sysusr
                );



        }

        DB::table('contadores')->insert($data);

    }
}
