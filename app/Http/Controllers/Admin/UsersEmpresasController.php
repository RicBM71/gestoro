<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersEmpresasController extends Controller
{

	public function update(Request $request, User $user)
    {

        $empresas =  $request->get('empresas');

        if ($request->get('seleccionadas') > 0)
            $user->update(['empresa_id' => $empresas[0]]);
        else {
            $user->update(['empresa_id' => 0]);
        }

        $user->syncEmpresas($request->get('empresas'));

        return response('Las empresas fueron actualizadas',200);
    }
}
