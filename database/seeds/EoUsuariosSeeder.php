<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EoUsuariosSeeder extends Seeder
{
    protected $bbdd="evaoro";

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
            ->select('select * from usuarios WHERE bloqueado="N"');

        foreach ($reg as $row){
            $data[]=array(
                'id'            => $row->id,
                'name'          => $row->nombre,
                'lastname'      => $row->apellidos,
                'email'         => null,
                'huella'        => $row->huella,
                'password'      =>Hash::make('123'),
                'avatar'        =>null,
                'blocked'       => $row->id==1 ? false : true,
                'blocked_at'    =>null,
                //'empresa_id'    =>$row->empdef,
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
                $user->assignRole(['root','admin']);
                DB::table('empresa_user')->insert(['empresa_id'=>7,'user_id'=>$row->id]);
            }
            $user->givePermissionTo('addcom');
            $user->givePermissionTo('addven');
            $user->givePermissionTo('liquidar');
            $user->givePermissionTo('factura');
        }

    }
}
