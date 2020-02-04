<?php

use App\Empresa;
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

        $contadores = DB::connection('quilates')->select('select * from crulara ORDER BY empresa');


        foreach ($contadores as $contador){

            session(['empresa' => Empresa::find($contador->emp_com)]);


            for ($ejercicio = 2008; $ejercicio <= 2020; $ejercicio++)
                $this->crearLineas($contador, $ejercicio);

        }
    }

    private function crearLineas($contador, $eje){


        $cruce_con = [
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 7,
            '7' => 8,
            '8' => 10,
            '9' => 11,
            '10' => 13, // fundido???? no vale pa na
            '11' => 13,
            '12' => 14
        ];


        $data=array();
        /// depósitos
        $reg = DB::connection($this->bbdd)
        ->select('select depositos.*, albaranes.cliente from albaranes,depositos '.
        ' where albaranes.empresa ='.$contador->empresa.' AND albaranes.tienda = '.$contador->tienda.
        ' and comven="C" '.
        ' and year(fechacomp) = '.$eje.
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
                'empresa_id'=> $contador->emp_com, // de depo
                'fecha' => $row->fecha,
                'cliente_id' => $row->cliente,
                'dias' => $row->dias,
                'concepto_id'=> $cruce_con[$row->concepto],
                'importe'=> $importe,
                'dias'=> $row->dias,
                'notas'=> $notas,
                'username' => $row->sysusr,
                'created_at' => $row->sysfum.' '.$row->syshum,
                'updated_at' => $row->sysfum.' '.$row->syshum,
            );

            if ($i % 2000 == 0){
                DB::table('depositos')->insert($data);
                $data=array();
            }

        }

        DB::table('depositos')->insert($data);

    }
}
