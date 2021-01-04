<?php

namespace App\Policies;

use App\User;
use App\Existencia;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExistenciaPolicy
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

    public function before($authUser)
    {
        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");
    }

    public function create(User $authUser, Existencia $existencia)
    {

        return $authUser->hasRole('Root') ?: $this->deny("Acceso denegado. Permiso Root requerido");

    }
    public function update(User $authUser, Existencia $existencia)
    {

        return $authUser->hasRole('Root') ?: $this->deny("Acceso denegado. Permiso Root requerido");

    }
    public function delete(User $authUser, Existencia $existencia)
    {

        return $authUser->hasRole('Root') ?: $this->deny("Acceso denegado. Permiso Root requerido");

    }
}
