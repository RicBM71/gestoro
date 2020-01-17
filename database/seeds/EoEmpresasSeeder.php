<?php

use App\Almacen;
use App\Empresa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EoEmpresasSeeder extends Seeder
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

        $reg = DB::connection('evaoro')->select('select * from empresas');

        foreach ($reg as $row){

            if ($row->id == 1)// es Evaoro.
                $obj = $row;


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
                'almacen_id'=> 1,
                'comun_empresa_id' => 1,
                'deposito_empresa_id' => $row->id,
                'username'  => $row->sysusr,
                'created_at'=> $row->sysfum.' 00:00:00',
                'updated_at'=> $row->sysfum.' '.$row->syshum,
            );
        }

        $row = $obj;
        $row->id = 7;
        $row->titulo= "Eva Oro Sol";
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
            'flags'     => '11100000000000000000',
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

        // $data=array();
        // $reg = DB::connection('evaoro')->select('select * from almacenes');
        // foreach ($reg as $row){
        //     $data[]=array(
        //         'id' => $row->id,
        //         'empresa_id' => 1,
        //         'nombre' => $row->nombre
        //     );
        // }

        // Almacen::insert($data);


        DB::table('empresa_user')->insert(
            ['empresa_id' => 1, 'user_id' => '1'],
            ['empresa_id' => 2, 'user_id' => '1'],
            ['empresa_id' => 3, 'user_id' => '1'],
            ['empresa_id' => 4, 'user_id' => '1'],
            ['empresa_id' => 5, 'user_id' => '1'],
            ['empresa_id' => 6, 'user_id' => '1'],
            ['empresa_id' => 7, 'user_id' => '1']
        );
    }
}
