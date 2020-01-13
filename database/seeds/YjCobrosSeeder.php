<?php

use App\Cobro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YjCobrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cobro::truncate();

        $reg = DB::connection('yajap')
            ->select('select depositos.*, albaranes.cliente from albaranes,depositos '.
            ' where albaranes.empresa <> 2'.
            ' and comven="V" '.
            ' and albaranes.id = depositos.albaran');

        $i=0;
        foreach ($reg as $row){

            $empresa_id = $row->tienda;
            if ($row->empresa == 3)
                $empresa_id  = 3;


            if ($row->concepto == 13)
                $fpago_id = 1;
            elseif($row->concepto == 14)
                $fpago_id = 4;
            elseif($row->concepto == 15)
                $fpago_id = 2;

            $data[]=array(

                'fecha'=>$row->fecha,
                'albaran_id'=>$row->albaran,
                'empresa_id'=> $empresa_id,
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
