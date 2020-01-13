<?php

namespace App\Policies;

use App\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function before($authUser)
    {

        if($authUser->hasRole('Root')){
            return true;
        }

    }

    public function view(User $user, Permission $permsion)
    {
        return $user->hasRole('Root');
    }
}
