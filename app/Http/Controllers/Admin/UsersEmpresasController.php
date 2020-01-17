<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UsersEmpresasController extends Controller
{

	public function update(Request $request, User $user)
    {

        $empresas =  $request->get('empresas');

        // if ($request->get('seleccionadas') > 0)
        //     $user->update(['empresa_id' => $empresas[0]]);
        // else {
        //     $user->update(['empresa_id' => 0]);
        // }

        if ($request->get('seleccionadas') > 0){

            DB::table('empresa_user')->where('user_id', $user->id)
                        ->update(['activa' => false]);

            DB::table('empresa_user')->where('user_id', $user->id)
                        ->where('empresa_id', $empresas[0])
                        ->update(['activa' => true]);

        }

       // $user->update(['empresa_id' => $empresas[0]]);

        $user->syncEmpresas($request->get('empresas'));

        return response('Las empresas fueron actualizadas',200);
    }
}
