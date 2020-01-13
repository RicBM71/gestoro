<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

// esto se ejecuta antes de cualquier método
    public function before($authUser)
    {
            // con esto ya no pasa por ningún otro método, lo dejo por si lo necesito para más adelante.
        if($authUser->hasRole('Root')){
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $authUser: es el usuario autenticado
     * @param  \App\User  $user: usuario que intentamos ver
     * @return mixed
     */
    public function view(User $authUser, User $user)
    {

        //dd($authUser->id);

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Role de administrador requerido");

        //return $authUser->hasPermissionTo('Usuarios');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermissionTo('users') ?: $this->deny("Acceso denegado. Role de administrador requerido");
       // return $authUser->hasPermissionTo('Usuarios');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $authUser, User $user)
    {
        
       if ($user->id == 1 && !$authUser->hasRole('Root')) return false;

       return $authUser->hasPermissionTo('users') ?: $this->deny("Acceso denegado. Permiso usuario requerido");

       // return $authUser->hasPermissionTo('Usuarios');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $authUser, User $user)
    {

        if ($user->id === 1)
            return false;

        return $authUser->hasRole('Root') ?: $this->deny("Acceso denegado. Role de ROOT requerido");
        //return $authUser->hasPermissionTo('Usuarios');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function restore(User $authUser, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function forceDelete(User $authUser, User $model)
    {
        //
    }


}
