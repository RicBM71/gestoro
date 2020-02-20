<?php

namespace App\Policies;

use App\User;
use App\Traspaso;
use Illuminate\Auth\Access\HandlesAuthorization;

class TraspasoPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(User $authUser, Traspaso $traspaso)
    {

        return true;
        //return $authUser->hasPermissionTo('authtras') ?: $this->deny("Acceso denegado. Permiso traspasos requerido");

    }

    public function update(User $authUser, Traspaso $traspaso)
    {
        if ($traspaso->situacion_id == 2) return true;

        return $authUser->hasPermissionTo('authtras') ?: $this->deny("Acceso denegado. Permiso traspasos requerido");


    }

    public function delete(User $authUser, Traspaso $traspaso)
    {

        return $authUser->hasRole('Admin')  ?: $this->deny("Acceso denegado. Permiso traspasos requerido");


    }


}
