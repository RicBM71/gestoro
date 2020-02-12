<?php

use App\Libro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EoLibrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Libro::truncate();

        $reg = DB::connection('evaoro')
            ->select('select contadores.*,tiendas.nombre  AS nomtienda from contadores, tiendas'.
                    ' WHERE contadores.tienda = tiendas.id AND compras > 1 '.
                    'ORDER BY contadores.id');


        foreach ($reg as $row){

            if ($row->tienda == 3) // pasamos tienda sol a empresa sol= 6
                $row->empresa = 7;

            if (strpos($row->nomtienda, 'Usados') === false)
                $crulib = "M";
            else
                $crulib = "U";

            if ($crulib == "M"){
                $grupo_id = 1;
                $nombre = "Metales";
                $nombre_csv = "metalprecioso";
                $empresa_id = $row->empresa;
                $semdia_bloqueo="3/1";
            }else{
                $grupo_id = 2;
                $nombre = "Usados";
                $empresa_id = $row->empresa;
                $semdia_bloqueo="1/1";
            }

            $data[]=array(
                'id'        => $row->id,
                'nombre'    => $nombre,
                'empresa_id'=> $empresa_id,
                'grupo_id'=> $grupo_id,
                'ejercicio'=>$row->ejercicio,
                'ult_compra'=>$row->compras - 1,
                'ult_factura'=>$row->factcompra -1,
                'serie_fac'=> 'R'.$row->serie,
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
