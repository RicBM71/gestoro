<?php

namespace App\Policies;

use App\User;
use App\Producto;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductoPolicy
{
    use HandlesAuthorization;



    public function before($authUser)
    {
       // if ($authUser->hasRole('Admin'))
            return true;
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
    public function create(User $authUser, Producto $producto)
    {

        return $authUser->hasRole('Admin') ?: $this->deny("Acceso denegado. Role de administrador requerido");

    }


     /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $authUser, Producto $producto)
    {

        return $authUser->hasPermissionTo('edtpro') ?: $this->deny("Acceso denegado. No puedes editar productos");

    }

}
