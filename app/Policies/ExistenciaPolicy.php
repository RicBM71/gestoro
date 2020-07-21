<?php

namespace App\Policies;

use App\User;
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
}
