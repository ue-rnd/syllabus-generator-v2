<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array($user->position, ['superadmin', 'dean', 'associate_dean', 'department_chair', 'faculty']) ||
               $user->hasPermissionTo('view departments');
    }

    public function view(User $user, Department $department): bool
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

        if ($user->position === 'faculty') {
            return true;
        }

        return $user->hasPermissionTo('view departments');
    }

    public function create(User $user): bool
    {
        return $user->position === 'superadmin' ||
               in_array($user->position, ['dean', 'associate_dean']) ||
               $user->hasPermissionTo('create departments');
    }

    public function createForCollege(User $user, $collegeId): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            // Check if the user has access to this specific college
            $accessibleCollegeIds = $user->getAccessibleColleges()->pluck('id')->toArray();

            return in_array($collegeId, $accessibleCollegeIds);
        }

        return $user->hasPermissionTo('create departments');
    }

    public function createForDepartment(User $user, $departmentId): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            $department = \App\Models\Department::find($departmentId);
            if (! $department) {
                return false;
            }

            $accessibleCollegeIds = $user->getAccessibleColleges()->pluck('id')->toArray();

            return in_array($department->college_id, $accessibleCollegeIds);
        }

        if ($user->position === 'department_chair') {
            return $user->department_id === $departmentId;
        }

        return $user->hasPermissionTo('create departments');
    }

    public function update(User $user, Department $department): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            return $user->college_id === $department->college_id ||
                   $department->college->dean_id === $user->id ||
                   $department->college->associate_dean_id === $user->id;
        }

        if ($user->position === 'department_chair') {
            return $department->department_chair_id === $user->id ||
                   $user->department_id === $department->id;
        }

        return $user->hasPermissionTo('edit departments');
    }

    public function delete(User $user, Department $department): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            return $user->college_id === $department->college_id ||
                   $department->college->dean_id === $user->id ||
                   $department->college->associate_dean_id === $user->id;
        }

        if ($user->position === 'department_chair') {
            return $department->department_chair_id === $user->id ||
                   $user->department_id === $department->id;
        }

        return $user->hasPermissionTo('delete departments');
    }

    public function restore(User $user, Department $department): bool
    {
        return $user->position === 'superadmin';
    }

    public function forceDelete(User $user, Department $department): bool
    {
        return $user->position === 'superadmin';
    }
}
