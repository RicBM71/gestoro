<?php

use App\Albalin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GdAlbalinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Albalin::truncate();

        $i=0;
        $alb = DB::connection('yajap')->select('select albalin.*,albaranes.empresa,albaranes.tienda,albaranes.tipo from albaranes,albalin WHERE empresa=2 and comven = "V" and albaranes.id=albalin.albaran ORDER BY albaranes.id');
        foreach ($alb as $row){

            $i++;

            $empresa_id = 1;

            if ($row->ivarige=='R'){
                $iva_id = 2;
            }elseif ($row->ivarige=='E'){
                $iva_id = 3;
            }elseif ($row->ivarige=='G'){
                $iva_id = 1;
            }elseif ($row->ivarige=='I'){
                $iva_id = 1;

            }

            $unidades = 1;
            if ($row->tipo == 4 and $row->peso <> 0 && $row->ivarige != 'I')
                $unidades = $row->peso;

            if ($row->tipo==3)
                $imp_uni = $row->importe;
            else{
                if ($row->ivarige == 'I')
                    $imp_uni = $row->importe;
                else
                    $imp_uni = $row->impgramo;
            }

            $data[]=array(
                'empresa_id' => $empresa_id,
                'albaran_id' => $row->albaran,
                'producto_id' => $row->producto,
                'unidades' => $unidades,
                'importe_unidad'=> $imp_uni,
                'precio_coste'=> $row->tipo==3 ? $row->pcoste : 0,
                'importe_venta'=>$row->importe,
                'iva_id' => $iva_id,
                'iva'=>$row->iva,
                'username'=>$row->sysusr,
                'created_at' => $row->sysfum.' '.$row->syshum,
                'updated_at' => $row->sysfum.' '.$row->syshum,
            );

            if ($i % 1000 == 0){
                DB::table('albalins')->insert($data);
                $data=array();
            }

        }

        DB::table('albalins')->insert($data);
    }
}
