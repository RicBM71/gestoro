<?php

use App\Cobro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EoCobrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cobro::truncate();

        $reg = DB::connection('evaoro')
            ->select('select depositos.*, albaranes.cliente from albaranes,depositos '.
            ' where albaranes.empresa > 0'.
            ' and comven="V" '.
            ' and albaranes.id = depositos.albaran');

        $i=0;
        foreach ($reg as $row){
            if ($row->tienda == 3)
                $row->empresa = 7;

            if ($row->concepto == 13)
                $fpago_id = 1;
            elseif($row->concepto == 14)
                $fpago_id = 4;
            elseif($row->concepto == 15)
                $fpago_id = 2;

            $data[]=array(

                'fecha'=>$row->fecha,
                'albaran_id'=>$row->albaran,
                'empresa_id'=> $row->empresa,
                'cliente_id' => $row->cliente,
                'fpago_id'=> $fpago_id,
                'importe'=>$row->importe,
                'notas'=>null,
                'username' => $row->sysusr,
                'created_at' => $row->sysfum.' '.$row->syshum,
                'updated_at' => $row->sysfum.' '.$row->syshum,
            );

            if ($i % 1000 == 0){
                DB::table('cobros')->insert($data);
                $data=array();
            }
        }
        DB::table('cobros')->insert($data);
    }
}
