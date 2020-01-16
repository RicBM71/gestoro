<?php

use App\Caja;
use Illuminate\Database\Seeder;

class GdCajasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Caja::truncate();

        $reg = DB::connection('yajap')
            ->select('select * from cajasmov '.
            ' where empresa = 2');

        $i=0;
        foreach ($reg as $row){

            $row->empresa = 1;

            $concepto = html_entity_decode($row->concepto);

            if (strpos($concepto, 'Depósito')!==false){
                $apunte_id = 1;
                $manual = "N";
            }
            elseif (strpos($concepto, 'Ampliación')!==false){
                $manual = "N";
                $apunte_id = 4;
            }
            elseif (strpos($concepto, 'A cuenta')!==false){
                $manual = "N";
                $apunte_id = 6;
            }
            elseif (strpos($concepto, 'Recuperado')!==false){
                $manual = "N";
                $apunte_id = 8;
            }
            elseif (strpos($concepto, 'Comprado')!==false){
                $manual = "N";
                $apunte_id = 11;
            }
            elseif (strpos($concepto, 'Venta')!==false){
                $manual = "N";
                $apunte_id = 21;
            }
            elseif (strpos($concepto, 'Cierre')!==false){
                $manual = "S";
                $apunte_id = 30;
            }
            else{
                $manual = $row->manual == 'S' ? 'S' : 'N';
                $apunte_id = null;
            }

            $data[]=array(

                'fecha'=>$row->fecha,
                'dh'=>$row->dh,
                'empresa_id'=> $row->empresa,
                'nombre' => $concepto,
                'importe'=> ($row->importe),
                'manual'=>  $manual,
                'apunte_id' => null,
                'username' => $row->sysusr,
                'created_at' => $row->sysfum.' '.$row->syshum,
                'updated_at' => $row->sysfum.' '.$row->syshum,
            );

            if ($i % 1000 == 0){
                DB::table('cajas')->insert($data);
                $data=array();
            }
        }
        DB::table('cajas')->insert($data);
    }
}
