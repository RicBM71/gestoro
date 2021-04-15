<?php

namespace App\Policies;

use App\User;
use App\Albaran;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlbaranPolicy
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

        if (!session('empresa')->getFlag(2)){
            return $this->deny("No se permiten ventas. Contactar administrador");
        }

        // if ($authUser->hasRole('Admin'))
        //     return true;
    }

      /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $authUser)
    {

        if (!session('empresa')->getFlag(4))
            return $this->deny("No se permiten NUEVAS ventas. Contactar administrador");

        if ($authUser->hasPermissionTo('addven'))
            return true;

        return $this->deny("Acceso denegado, no dispone de los permisos requeridos");
    }

     /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Albaran  $model
     * @return mixed
     */
    public function update(User $authUser, Albaran $albarane)
    {

        //if ($authUser->hasPermissionTo('addven'))
            return true;

        //return $this->deny("Acceso denegado, no dispone de los permisos requeridos");

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Albaran  $model
     * @return mixed
     */
    public function delete(User $authUser, Albaran $albarane)
    {

        if (esPropietario($albarane)){
            return true;
        }elseif(!hasDelAlb()){
                return $this->deny("Acceso denegado!. Permiso borrado albarán requerido");
            }


        return true;

    }

}
