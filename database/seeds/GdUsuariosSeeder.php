<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GdUsuariosSeeder extends Seeder
{
    protected $bbdd="yajap";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::truncate();


        /// depósitos
        $reg = DB::connection($this->bbdd)
            ->select('select * from usuarios WHERE id in(1,6,7,9) order by id');

        $id=0;
        foreach ($reg as $row){

            $id++;
            $data[]=array(
                'id'            => $id,
                'name'          => $row->nombre,
                'lastname'      => $row->apellidos,
                'email'         => null,
                'huella'        => $row->huella,
                'password'      =>Hash::make('123'),
                'avatar'        =>null,
                'blocked'       => $row->id==1 ? false : true,
                'blocked_at'    =>null,
            //    'empresa_id'    =>1,
                'login_at'  => null,
                'expira'    => false,
                'fecha_expira' => date('Y-m-d'),
                'username' => $row->usuario,
                'username_umod'=> $row->sysusr,
                'created_at' => $row->sysfum.' '.$row->syshum,
                'updated_at' => $row->sysfum.' '.$row->syshum,
            );

            $empresas = explode(',',$row->empresas);

            foreach ($empresas as $emp){

                DB::table('empresa_user')->insert(['empresa_id'=>$emp,'user_id'=>$row->id]);
            }
        }

        DB::table('users')->insert($data);

        $users = User::all();
        foreach ($users as $user){
            if ($user->id == 1){
                $user->assignRole(['root','admin','gestor']);
            }
            $user->givePermissionTo('addcom');
            $user->givePermissionTo('addven');
            $user->givePermissionTo('liquidar');
            $user->givePermissionTo('factura');
        }

    }
}
