<?php

use App\Comline;
use App\Empresa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KltComlinesSeeder extends Seeder
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


        Comline::truncate();

        $contadores = DB::connection('quilates')->select('select * from crulara ORDER BY empresa');


        foreach ($contadores as $contador){

            session(['empresa' => Empresa::find($contador->emp_com)]);


            for ($ejercicio = 2008; $ejercicio <= 2020; $ejercicio++)
                $this->crearLineas($contador, $ejercicio);

        }
    }

    private function crearLineas($contador, $eje){

        $data=array();
        $reg = DB::connection($this->bbdd)->select('select albalin.*,albaranes.empresa from albaranes,albalin '.
        ' where albaranes.empresa ='.$contador->empresa.' AND albaranes.tienda = '.$contador->tienda.
        ' and comven="C" '.
        ' and year(fechacomp) = '.$eje.
        ' and albaranes.id = albalin.albaran and linreg="N" and producto=0');

        $i=0;
        foreach ($reg as $row){
            $i++;

            switch ($row->tipo){
                case "O":
                    $clase_id=1;
                    break;
                case "P":
                    $clase_id=2;
                    break;
                case "T":
                    $clase_id=3;
                    break;
                case "I":
                    $clase_id=4;
                    break;
                case "A": // brillante
                    $clase_id=6;
                    break;
                case "B": // reloj
                    $clase_id=7;
                    break;
                case "C": // otros
                    $clase_id=8;
                    break;
                default:
                    $clase_id=5; // otros metales
                    break;
            }


            $data[]=array(
                'id'    => $row->id,
                'compra_id' => $row->albaran,
                'empresa_id'=> $contador->emp_com,
                'concepto' => $row->concepto,
                'grabaciones' => $row->grabaciones,
                'colores' => $row->colores,
                'clase_id' => $clase_id,
                'peso_gr' => $row->peso,
                'importe' => $row->importe,
                'importe_gr' => $row->impgramo,
                'iva'       => $row->iva,
                'importe_venta'=> $row->pcoste, // esto es así por facturas de recuperación.
                'quilates' => $row->quilates,
                'fecha_liquidado' => $row->fechafun=="0000-00-00" ? null : $row->fechafun,
                'username' => $row->sysusr,
                'created_at' => $row->sysfum.' '.$row->syshum,
                'updated_at' => $row->sysfum.' '.$row->syshum,
            );

            if ($i % 1000 == 0){
                DB::table('comlines')->insert($data);
                $data=array();
            }

        }

        DB::table('comlines')->insert($data);

    }
}
