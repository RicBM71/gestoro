<?php

use App\Deposito;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KltDepositosSeeder extends Seeder
{

    protected $bbdd="quilates";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $empresa = '1,9,12,16';
        $eje = '>=2000';

        Deposito::truncate();

        /// depósitos
        $reg = DB::connection($this->bbdd)
        ->select('select depositos.*, albaranes.cliente from albaranes,depositos '.
        ' where albaranes.empresa in('.$empresa.')'.
    //' and comven="C" and year(fechacomp) >= '.$eje.
        ' and comven="C" '.
        ' and year(fechacomp) '.$eje.
        ' and albaranes.id = depositos.albaran');

        $data=array();
        $i=0;
        foreach ($reg as $row){
            $i++;

            $notas = str_replace('&iacute;', 'í', $row->notas);
            $notas = str_replace('&euro;', '', $notas);

            if ($row->concepto == 6 || $row->concepto == 7) // cambiamos el signo pagos a cuenta, a veces "recapitalizan"
                $importe = $row->importe * -1;
            else
                $importe = abs($row->importe);

            $data[]=array(
                'id'    => $row->id,
                'compra_id' => $row->albaran,
                'empresa_id'=> $row->empresa, // de depo
                'fecha' => $row->fecha,
                'cliente_id' => $row->cliente,
                'dias' => $row->dias,
                'concepto_id'=> $row->concepto,
                'importe'=> $importe,
                'dias'=> $row->dias,
                'notas'=> $notas,
                'username' => $row->sysusr,
                'created_at' => $row->sysfum.' '.$row->syshum,
                'updated_at' => $row->sysfum.' '.$row->syshum,
            );

            if ($i % 1000 == 0){
                DB::table('depositos')->insert($data);
                $data=array();
            }

        }

        DB::table('depositos')->insert($data);

    }
}
