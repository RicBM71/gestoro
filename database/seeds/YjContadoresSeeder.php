<?php

use App\Contador;
use Illuminate\Database\Seeder;

class YjContadoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Contador::truncate();

        $reg = DB::connection('yajap')
        ->select('select contadores.* from contadores'.
                ' WHERE albaran > 0 and empresa<>2 '.
                'ORDER BY contadores.id');

        foreach($reg as $row){

            // if ($row->tienda == 5 || $row->albaran == 1 || $row->albaran == 1000)
            //     continue;

            // if ($row->ejercicio <> 2020 && $row->albaran == 1001 )
            //     continue;

            $empresa_id = $row->tienda;
            if ($row->empresa == 3)
                $empresa_id  = 3;

            if (strlen($row->atserie) > 3)
                $atserie = substr($row->atserie,0,3);
            else
                $atserie = $row->atserie;

                /// mete contado rebu
            $data[]=array(
                'empresa_id'=> $empresa_id,
                'tipo_id'=> 3,
                'ejercicio'=>$row->ejercicio,
                'ult_albaran'=>$row->albaran - 1,
                'serie_albaran' => 'R',
                'ult_factura'=> $row->factura - 1,
                'serie_factura'=>$row->serie,
                'ult_factura_auto'=>$row->atfactura - 1,
                'serie_factura_auto'=>$atserie,
                'serie_factura_abono'=>'FR',
                'ult_factura_abono'=>0,
                'cerrado' => $row->ejercicio == 2020 ? false: true,
                'created_at' => $row->sysfum.' 00:00:00',
                'updated_at' => $row->sysfum.' '.$row->syshum,
                'username'=> $row->sysusr
            );

                // mete contador general si hay algo
            if ($row->gefactura > 1)
                $data[]=array(
                    'empresa_id'=> $empresa_id,
                    'tipo_id'=> 4,
                    'ejercicio'=>$row->ejercicio,
                    'ult_albaran'=>$row->albaran - 1,
                    'serie_albaran'=>'V',
                    'ult_factura'=> $row->gefactura - 1,
                    'serie_factura'=>$row->geserie,
                    'ult_factura_auto'=>0,
                    'serie_factura_auto'=>'VX',
                    'serie_factura_abono'=>'VFR',
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
