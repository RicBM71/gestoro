<?php

namespace App\Policies;

use App\User;
use App\Cliente;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientePolicy
{
    use HandlesAuthorization;


// esto se ejecuta antes de cualquier método
public function before($authUser)
{
    return true;
    //return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. No puedes editar clientes");
        // con esto ya no pasa por ningún otro método, lo dejo por si lo necesito para más adelante.
    // if($authUser->hasRole('Root') || $authUser->hasRole('Admin')){
    //     return true;
    //}
}


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
    public function update(User $authUser, Cliente $cliente)
    {

        //return $authUser->hasRole('Supervisor') ?: $this->deny("Acceso denegado. No puedes editar clientes");
        //return $authUser->hasPermissionTo('Edita Clientes') ?: $this->deny("Acceso denegado. No puedes editar clientes");
        return true;

    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $authUser, Cliente $cliente)
    {

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado, solo un administrador puede borrar clientes");

    }
}
