<?php

namespace App\Policies;

use App\Caja;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CajaPolicy
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


     /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $authUser, Caja $caja)
    {
        if ($caja->manual == 'N'){
            return $this->deny('El apunte es automático, no se puede modificar');
        }

        if ($caja->manual == 'C'){
             return $this->deny('El apunte de cierre no se puede modificar');
        }

        if ($caja->manual == 'R' && !$authUser->hasRole('Admin') ){
            return $this->deny('Acceso denegado. Contactar con un administrador');
        }


        if ($authUser->hasRole('Admin') || $authUser->hasRole('Supervisor') )
            return true;

        return esPropietario($caja) ?: $this->deny("Acceso denegado. No puedes editar el registro");


    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $authUser, Caja $caja)
    {

        if ($caja->manual == 'N'){
            return $this->deny('El apunte es automático, no se puede borrar');
        }

        if ($caja->manual == 'C' && $authUser->hasRole('Supervisor'))
            return true;

        if ($authUser->hasRole('Admin'))
            return true;

        return esPropietario($caja) ?: $this->deny("Acceso denegado. No puedes borrar el registro");

    }

}
