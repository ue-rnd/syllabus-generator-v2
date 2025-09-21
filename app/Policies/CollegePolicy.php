<?php

namespace App\Policies;

use App\Models\College;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollegePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array($user->position, ['superadmin', 'dean', 'associate_dean', 'department_chair', 'faculty']) ||
               $user->hasPermissionTo('view colleges');
    }

    public function view(User $user, College $college): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean', 'department_chair', 'faculty'])) {
            return true;
        }

        return $user->hasPermissionTo('view colleges');
    }

    public function create(User $user): bool
    {
        return $user->position === 'superadmin' ||
               $user->hasPermissionTo('create colleges');
    }

    public function update(User $user, College $college): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            return $college->dean_id === $user->id ||
                   $college->associate_dean_id === $user->id;
        }

        return $user->hasPermissionTo('edit colleges');
    }

    public function delete(User $user, College $college): bool
    {
        return $user->position === 'superadmin' ||
               $user->hasPermissionTo('delete colleges');
    }

    public function restore(User $user, College $college): bool
    {
        return $user->position === 'superadmin';
    }

    public function forceDelete(User $user, College $college): bool
    {
        return $user->position === 'superadmin';
    }
}
