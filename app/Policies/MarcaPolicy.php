<?php

namespace App\Policies;

use App\User;
use App\Marca;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarcaPolicy
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

    // esto se ejecuta antes de cualquier método
    public function before($authUser)
    {
        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");
    }

    public function create(User $authUser, Marca $marca)
    {

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");

    }
    public function update(User $authUser, Marca $marca)
    {

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");

    }
    public function delete(User $authUser, Marca $marca)
    {

        return $authUser->hasRole('Root') ?: $this->deny("Acceso denegado. Permiso root requerido");

    }

}
