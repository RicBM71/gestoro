<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersPermissionsController extends Controller
{

	public function update(Request $request, User $user)
    {

        //return $request->permissions;

        $user->syncPermissions($request->permissions); // esto es del paquete laravel permisions

        return response('Los permisos fueron actualizados',200);
    }
}
