<?php

namespace App\Policies;

use App\User;
use App\Libro;
use App\Compra;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompraPolicy
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
        if (!session('empresa')->getFlag(1)){
            return $this->deny("No se permiten compras. Contactar administrador");
        }

    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $authUser)
    {

        if (!session('empresa')->getFlag(3))
            return $this->deny("No se permiten NUEVAS compras. Contactar administrador");

        if ($authUser->hasPermissionTo('addcom'))
            return true;

        return $this->deny("Acceso denegado, no dispone de permiso para crear compras");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Compra  $model
     * @return mixed
     */
    public function update(User $authUser, Compra $compra)
    {
        if ($compra->fase_id == 1)
            return true;

        if (hasEdtInt() || esPropietario($compra))
            return true;

        return $this->deny("Acceso denegado, no dispone de los permisos requeridos");

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Compra  $model
     * @return mixed
     */
    public function delete(User $authUser, Compra $compra)
    {

        if ($authUser->hasPermissionTo('delcom'))
            return true;

        if (esPropietario($compra)){
            $libro =  Libro::where('grupo_id',$compra->grupo_id)
                                ->where('ejercicio',$compra->ejercicio)
                                ->firstOrFail();
            if ($libro->ult_compra == $compra->albaran){
                return true;
            }else{
                return $this->deny("Acceso denegado! Hay compras posteriores. Permiso Borrar Compra requerido");
            }
        }

        return $this->deny("Acceso denegado, no dispone de los permisos requeridos");

    }

}
