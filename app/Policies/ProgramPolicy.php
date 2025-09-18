<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProgramPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array($user->position, ['superadmin', 'dean', 'associate_dean', 'department_chair']) ||
               $user->hasPermissionTo('view programs');
    }

    public function view(User $user, Program $program): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            return true;
        }

        if ($user->position === 'department_chair') {
            return true;
        }

        return $user->hasPermissionTo('view programs');
    }

    public function create(User $user): bool
    {
        return $user->position === 'superadmin' ||
               in_array($user->position, ['dean', 'associate_dean', 'department_chair']) ||
               $user->hasPermissionTo('create programs');
    }

    public function createForDepartment(User $user, $departmentId): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            // Check if the department belongs to a college they have access to
            $department = \App\Models\Department::find($departmentId);
            if (! $department) {
                return false;
            }

            $accessibleCollegeIds = $user->getAccessibleColleges()->pluck('id')->toArray();

            return in_array($department->college_id, $accessibleCollegeIds);
        }

        if ($user->position === 'department_chair') {
            // Check if this is their department
            $accessibleDepartmentIds = $user->getAccessibleDepartments()->pluck('id')->toArray();

            return in_array($departmentId, $accessibleDepartmentIds);
        }

        return $user->hasPermissionTo('create programs');
    }

    public function update(User $user, Program $program): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            return $user->college_id === $program->department->college_id ||
                   $program->department->college->dean_id === $user->id ||
                   $program->department->college->associate_dean_id === $user->id;
        }

        if ($user->position === 'department_chair') {
            return $program->department->department_chair_id === $user->id ||
                   $user->department_id === $program->department_id;
        }

        return $user->hasPermissionTo('edit programs');
    }

    public function delete(User $user, Program $program): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            return $user->college_id === $program->department->college_id ||
                   $program->department->college->dean_id === $user->id ||
                   $program->department->college->associate_dean_id === $user->id;
        }

        if ($user->position === 'department_chair') {
            return $program->department->department_chair_id === $user->id ||
                   $user->department_id === $program->department_id;
        }

        return $user->hasPermissionTo('delete programs');
    }

    public function restore(User $user, Program $program): bool
    {
        return $user->position === 'superadmin';
    }

    public function forceDelete(User $user, Program $program): bool
    {
        return $user->position === 'superadmin';
    }
}
