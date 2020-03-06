<?php

namespace App\Policies;

use App\User;
use App\Cruce;
use Illuminate\Auth\Access\HandlesAuthorization;

class CrucePolicy
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
        return $authUser->hasRole('Root') ?: $this->deny("Acceso denegado. Permiso root requerido");
    }

    public function create(User $authUser, Cruce $cruce)
    {

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");

    }
    public function update(User $authUser, Cruce $cruce)
    {
        // \Log::info($cruce->empresa_id);
        // \Log::info(session('empresa_id'));
        // if ($cruce->empresa_id <> session('empresa_id')){
        //     return $this->deny("Acceso denegado.");
        // }

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");

    }
    public function delete(User $authUser, Cruce $cruce)
    {

        if ($cruce->empresa_id =! session('empresa_id')){
            return $this->deny("Acceso denegado.");
        }

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Permiso administrador requerido");

    }

}
