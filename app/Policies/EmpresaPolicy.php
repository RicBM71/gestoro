<?php

namespace App\Policies;

use App\User;
use App\Empresa;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmpresaPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    // esto se ejecuta antes de cualquier método
    public function before($authUser)
    {
        if ($authUser->hasRole('Root'))
            return true;
    }

    public function create(User $authUser, Empresa $empresa)
    {

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");

    }
    public function update(User $authUser, Empresa $empresa)
    {

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");

    }
    public function delete(User $authUser, Empresa $empresa)
    {

        return $authUser->hasRole('Root') ?: $this->deny("Acceso denegado. Permiso administrador requerido");

    }
}
