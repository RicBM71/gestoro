<?php

use App\Almacen;
use App\Empresa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KltEmpresaSeeder extends Seeder
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

        //$reg = DB::connection('quilates')->select('SELECT DISTINCT empresa,tienda, empresas.razon, tiendas.nombre FROM `albaranes` join empresas on empresas.id = albaranes.empresa join tiendas on tiendas.id = albaranes.tienda');
        $reg = DB::connection('quilates')->select('SELECT * from crulara');
        $empresas_creadas=array();

        $i=0;
        foreach ($reg as $row){

            if (in_array($row->emp_com, $empresas_creadas))
                continue;

            $empresas_creadas[]=$row->emp_com;

            // $e = DB::connection('quilates')->select('SELECT * from empresas WHERE id='.$row->empresa);
            // \Log::info($e[0]);
            // foreach ($e as $emp){

            //     \Log::info($emp);
            // }
            $emp = DB::connection('quilates')->table('empresas')->select('*')->where('id', $row->empresa)->first();

          //  \Log::info($emp->nombre);

            $tie = DB::connection('quilates')->table('tiendas')->select('*')->where('id', $row->tienda)->first();



            //$emp = $emp[0];
            // if ($row->id == 1)// es Kilates ó celenque (esta la duplicamos a manuqui)
            //     $obj = $row;

            // if ($row->id == 3){
            //     $row = $obj;
            //     $row->id = 3;
            //     $row->titulo= "Bombonera";
            // }

            $data[]=array(
                'id'        => $row->emp_com,
                'nombre'    => $emp->nombre, //.' '.$tie->nombre,
                'razon'     => $emp->razon,
                'cif'       => $emp->cif,
                'poblacion' => $emp->poblacion,
                'direccion' => $emp->direccion,
                'cpostal'   => $emp->cpostal,
                'provincia' => $emp->provincia,
                'telefono1' => $emp->telefono,
                'telefono2' => $emp->fax,
                'contacto'  => $emp->contacto,
                'email'     => $emp->email,
                'web'       => $emp->web,
                'txtpie1'   => $emp->txtpie1,
                'txtpie2'   => $emp->txtpie2,
                'flags'     => '11100000000000000000',
                'sigla'     => $emp->sigla,
                'titulo'    => $emp->titulo,
                'almacen_id'=> null,
                'comun_empresa_id' => 1,
                'deposito_empresa_id' => $row->emp_com,
                'username'  => $emp->sysusr,
                'created_at'=> $emp->sysfum.' 00:00:00',
                'updated_at'=> $emp->sysfum.' '.$emp->syshum,
            );
        }

        Empresa::insert($data);

        // $data=array();
        // $reg = DB::connection('quilates')->select('select * from almacenes');
        // foreach ($reg as $emp){
        //     $data[]=array(
        //         'id' => $emp->id,
        //         'empresa_id'=> 1,
        //         'nombre' => $emp->nombre
        //     );
        // }

        //Almacen::insert($data);




        DB::table('empresa_user')->insert(
            ['empresa_id' => 1, 'user_id' => '1'],
            ['empresa_id' => 11, 'user_id' => '1'],
            ['empresa_id' => 1, 'user_id' => '2']
        );
    }
}
