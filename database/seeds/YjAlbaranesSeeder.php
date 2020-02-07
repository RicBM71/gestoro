<?php

use App\Albaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YjAlbaranesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        Albaran::truncate();

        $i=0;
        $alb = DB::connection('yajap')->select('select * from albaranes WHERE comven = "V" and empresa<>2 ORDER BY id');
        foreach ($alb as $row){
            $i++;

            $empresa_id = $row->tienda;
            if ($row->empresa == 3)
                $empresa_id  = 3;

            if (strlen($row->serie) > 3)
                $row->serie = substr($row->serie,0,3);

            if ($empresa_id == 2)
                $tipo = $row->tipo == 3 ? 'C' : 'D';
            else
                $tipo = $row->tipo == 3 ? 'R' : 'V';


            $data[]=array(
                'empresa_id' => $empresa_id,
                'id'=> $row->id,
                'tipo_id' => $row->tipo,
                'serie_albaran' => $tipo,
                'albaran' => $row->albaran,
                'fase_id' => $row->estado==20 ? 11 : $row->estado,
                'cliente_id' => $row->cliente,
                'fecha_albaran' => $row->fechacomp,
                'fecha_factura' => $row->fechafac=='0000-00-00' ? null : $row->fechafac,
                'factura'=> $row->factura==0 ? null : $row->factura,
                'serie_factura'=>$row->serie == '' ? null : $row->serie,
                'tipo_factura' => $row->tipofac == 'M' ? 1 : 2,
                'clitxt' => $row->clitxt=='' ? null : $row->clitxt,
                'fecha_notificacion'=>$row->fnotifica=='0000-00-00' ? null : $row->fnotifica,
                'online' => $row->online == 'N' ? false: true,
                'iva_no_residente' => $row->exentoiva == 'N' ? false: true,
                'notas_int' => $row->notas == '' ? null : $row->notas,
                'motivo_id' => null,
                'factura_txt' => $row->factura > 0 ? $empresa_id.getEjercicio($row->fechafac).$row->serie.$row->factura : null,
                'albaran_abonado_id'=>null,
                'username'=>$row->sysusr,
                'cuenta_id'=>null,
                'fpago_id'=>$row->tipo == 3 ? null : 2,
                'facturar'=> true,
                'taller_id'=>null,
                'created_at' => $row->sysfum.' '.$row->syshum,
                'updated_at' => $row->sysfum.' '.$row->syshum,

            );

            if ($i % 1000 == 0){
                DB::table('albaranes')->insert($data);
                $data=array();
            }

        }


        DB::table('albaranes')->insert($data);


    }


}
