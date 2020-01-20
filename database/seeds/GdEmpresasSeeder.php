<?php

use App\Almacen;
use App\Empresa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GdEmpresasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Empresa::truncate();
        Almacen::truncate();
        DB::table('empresa_user')->truncate();

        $reg = DB::connection('yajap')->select('select * from empresas where id=2');

        foreach ($reg as $row){

            $row->id =1;

            $data[]=array(
                'id'        => $row->id,
                'nombre'    => $row->nombre,
                'razon'     => $row->razon,
                'cif'       => $row->cif,
                'poblacion' => $row->poblacion,
                'direccion' => $row->direccion,
                'cpostal'   => $row->cpostal,
                'provincia' => $row->provincia,
                'telefono1' => $row->telefono,
                'telefono2' => $row->fax,
                'contacto'  => $row->contacto,
                'email'     => $row->email,
                'web'       => $row->web,
                'txtpie1'   => $row->txtpie1,
                'txtpie2'   => $row->txtpie2,
                'flags'     => '11111000000000000000',
                'sigla'     => $row->sigla,
                'titulo'    => $row->titulo,
                'almacen_id'=> 0,
                'comun_empresa_id' => 1,
                'deposito_empresa_id' => 1,
                'username'  => $row->sysusr,
                'created_at'=> $row->sysfum.' 00:00:00',
                'updated_at'=> $row->sysfum.' '.$row->syshum,
            );

            Empresa::insert($data);
        }



        // $data2[]=array(
        //     'id' => 1,
        //     'empresa_id' => 1,
        //     'nombre' => 'Gd Porlier'
        // );

        // Almacen::insert($data2);




        // DB::table('empresa_user')->insert(
        //     ['empresa_id' => 1, 'user_id' => '1']
        // );
    }
}
