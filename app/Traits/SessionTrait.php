<?php

namespace App\Traits;

use App\User;
use App\Empresa;
use App\Parametro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait SessionTrait {

    /**
     * Recarga valores de sessión
     *
     * @param [type] $empresa_id
     * @return void
     */
    public function loadSession($empresa_id) {

        $authUser = auth()->user();

        $role_user=[];
        $data = User::find($authUser->id)->roles()->get();
        foreach($data as $role){
            $role_user[]=$role->name;
        }

        $permisos_user=[];
        //$data = User::find($authUser->id)->permissions()->get();
        $data = auth()->user()->getAllPermissions();
        foreach($data as $permiso){
            $permisos_user[]=$permiso->name;
        }

        $empresas_usuario = collect();
        foreach ($authUser->empresas as $empresa){
            if ($empresa->flags[0] == false)
                continue;

            //$empresa_id = $empresa->id;

            $empresas_usuario->push($empresa->id);
            $empresas[] = [
                'value' => $empresa->id,
                'text' => $empresa->titulo
            ];
        }


        $parametros = Parametro::find(1);


        $empresa = Empresa::findOrFail($empresa_id);

        $user = [
            'id'            => $authUser->id,
            'name'          => $authUser->name,
            'username'      => $authUser->username,
            'avatar'        => $authUser->avatar,
            'empresa_id'    => $empresa_id,
            'empresa_nombre'=> $empresa->titulo,
            'roles'         => $role_user,
            'permisos'      => $permisos_user,
            'empresas'      => $empresas,
            'parametros'    =>$parametros,
            'img_fondo'        => $empresa->img_fondo,
            'aislar_empresas'  => $parametros->aislar_empresas
        ];

        session([
            'empresa_id' => $empresa_id,
            'empresa'    => $empresa,
            'username'   => $authUser->username,
            'empresas_usuario' => $empresas_usuario,
            'parametros'       => $parametros,
            'aislar_empresas'  => $parametros->aislar_empresas
            ]);



        DB::table('empresa_user')->where('user_id', $authUser->id)
            ->update(['activa' => false]);

        DB::table('empresa_user')->where('user_id', $authUser->id)
                    ->where('empresa_id', $empresa_id)
                    ->update(['activa' => true]);


        return [
            'user'      => $user,
            'authuser'  =>$authUser,
        ];


    }

}
