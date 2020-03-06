<?php

use App\Libro;
use Illuminate\Database\Seeder;

class KltLibrosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Libro::truncate();

                $reg = DB::connection('quilates')
            //->select('select * from contadores WHERE id=148');
            ->select('select * from contadores '.
                    ' WHERE compras > 1 '.
                    ' AND ejercicio >= 1 '.
                    ' ORDER BY contadores.id');


        foreach ($reg as $row){

            //\Log::info($row->empresa.' Ti: '.$row->tienda.' ejer: '.$row->ejercicio);

            // if ($row->tienda == 6) // saltamos bombonera, que será la empresa 3 cuando importemos ventas.
            //     continue;

            $c = DB::connection('quilates')->select('select * from crulara where empresa ='.$row->empresa.' AND tienda ='.$row->tienda);

            foreach ($c as $cruce){
                \Log::info($cruce->libro.' T:'.$row->tienda);
            }




            // if (strpos($row->nomtienda, 'Usados') === false)
            //     $crulib = "M";
            // else
            //     $crulib = "U";
            $crulib = $cruce->libro;

            if ($crulib == "M"){
                $grupo_id = 1;
                $nombre = "Metales";
                $nombre_csv = "metalprecioso";
                $empresa_id = $row->empresa;
                $semdia_bloqueo="3/1";
            }else{
                $grupo_id = 2;
                $nombre = "Usados";
                $nombre_csv = "usados";
                $empresa_id = $row->empresa;
                $semdia_bloqueo="1/1";
            }

            if ($row->empresa == 14 && $row->ejercicio == 2020){
                continue;
            }

            if (strlen($row->serie) > 3)
                $row->serie = substr($row->serie,0,2);

            $data[]=array(
                'id'        => 0,
                'nombre'    => $nombre,
                'empresa_id'=> $cruce->emp_com,
                'grupo_id'=> $grupo_id,
                'ejercicio'=>$row->ejercicio,
                'ult_compra'=>$row->compras - 1,
                'ult_factura'=>$row->factcompra -1,
                'serie_fac'=>'R'.$row->serie,
                'serie_com'=>$crulib,
                'semdia_bloqueo'=>$semdia_bloqueo,
                'dias_custodia'=>30,
                'dias_cortesia'=>7,
                'interes'=>10,
                'codigo_pol'=>null,
                'nombre_csv'=> $nombre_csv,
                'cerrado'=> $row->ejercicio==date('Y')? false: true,
                'grabaciones'=>false,
                'created_at' => $row->sysfum.' 00:00:00',
                'updated_at' => $row->sysfum.' '.$row->syshum,
                'username'=> $row->sysusr
            );
        }
        // hacerlo así mejor para evitar problemas con ScopeEmpresa
        DB::table('libros')->insert($data);
        //Libro::insert($data);
    }
}
