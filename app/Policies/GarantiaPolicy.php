<?php

namespace App\Policies;

use App\User;
use App\Garantia;
use Illuminate\Auth\Access\HandlesAuthorization;

class GarantiaPolicy
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

    public function create(User $authUser, Garantia $garantia)
    {

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");

    }

    public function update(User $authUser, Garantia $garantia)
    {

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");

    }

    public function delete(User $authUser, Garantia $garantia)
    {

        return $authUser->hasRole('Root') ?: $this->deny("Acceso denegado. Permiso administrador root");

    }

}
