<?php

namespace App\Policies;

use App\User;
use App\Taller;
use Illuminate\Auth\Access\HandlesAuthorization;

class TallerPolicy
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

   
    public function create(User $authUser, Taller $taller)
    {

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");

    }

    public function update(User $authUser, Taller $taller)
    {

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");

    }

    public function delete(User $authUser, Taller $taller)
    {

        return $authUser->hasRole('Root') ?: $this->deny("Acceso denegado. Permiso administrador root");

    }

}
