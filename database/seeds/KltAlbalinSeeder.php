<?php

use App\Albalin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KltAlbalinSeeder extends Seeder
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
        $alb = DB::connection('quilates')->select('select albalin.*,albaranes.empresa,albaranes.tienda,albaranes.tipo from albaranes,albalin WHERE comven = "V" and albaranes.id=albalin.albaran ORDER BY albaranes.id');
        $emp_ant = $tie_ant = -1;
        foreach ($alb as $row){

            if ($emp_ant <> $row->empresa || $tie_ant <> $row->tienda){
                $r = DB::connection('quilates')->select('select * from crulara WHERE empresa ='.$row->empresa.' AND tienda='.$row->tienda);
                foreach ($r as $cruce){
                }
                $emp_ant = $row->empresa;
                $tie_ant = $row->tienda;
            }


            $i++;

            if ($row->ivarige=='R'){
                $iva_id = 2;
            }elseif ($row->ivarige=='E'){
                $iva_id = 3;
            }elseif ($row->ivarige=='G'){
                $iva_id = 1;
            }

            $unidades = 1;
            if ($row->tipo == 4 and $row->peso <> 0)
                $unidades = $row->peso;

            $data[]=array(
                'empresa_id' => $cruce->emp_alb,
                'albaran_id' => $row->albaran,
                'producto_id' => $row->producto,
                'unidades' => $unidades,
                'importe_unidad'=>$row->tipo==3 ? $row->importe : $row->impgramo,
                'precio_coste'=>$row->tipo==3 ? $row->pcoste : 0,
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
