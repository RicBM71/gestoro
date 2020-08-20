<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FixingPolicy
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
         return esAdmin() ?: $this->deny("Acceso denegado. Permiso admin requerido");
     }

}
