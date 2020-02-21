<?php

use App\Comline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EoComlinesSeeder extends Seeder
{
    protected $bbdd="evaoro";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $eje = '>=2000';


        Comline::truncate();

        $reg = DB::connection($this->bbdd)->select('select albalin.*,albaranes.empresa,albaranes.tienda from albaranes,albalin '.
        // ' where albaranes.empresa > 0'.
        ' where albaranes.id in (31,62,95,99)'.
        ' and comven="C" '.
        ' and year(fechacomp) '.$eje.
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

            if ($row->tienda == 3)
                $row->empresa = 7;

            $data[]=array(
                'id'    => $row->id,
                'compra_id' => $row->albaran,
                'empresa_id'=> $row->empresa,
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
