<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array($user->position, ['superadmin', 'dean', 'associate_dean', 'department_chair', 'faculty']) ||
               $user->hasPermissionTo('view courses');
    }

    public function view(User $user, Course $course): bool
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

        return $user->hasPermissionTo('view courses');
    }

    public function create(User $user): bool
    {
        return $user->position === 'superadmin' ||
               in_array($user->position, ['dean', 'associate_dean', 'department_chair']) ||
               $user->hasPermissionTo('create courses');
    }

    public function createForCollege(User $user, $collegeId): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            $accessibleCollegeIds = $user->getAccessibleColleges()->pluck('id')->toArray();

            return in_array($collegeId, $accessibleCollegeIds);
        }

        if ($user->position === 'department_chair') {
            // Department chairs can create courses in their college
            return $user->college_id === $collegeId;
        }

        return $user->hasPermissionTo('create courses');
    }

    public function update(User $user, Course $course): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            return $user->college_id === $course->college_id ||
                   $course->college->dean_id === $user->id ||
                   $course->college->associate_dean_id === $user->id;
        }

        if ($user->position === 'department_chair') {
            return $user->department_id &&
                   $course->college_id === $user->college_id;
        }

        return $user->hasPermissionTo('edit courses');
    }

    public function delete(User $user, Course $course): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            return $user->college_id === $course->college_id ||
                   $course->college->dean_id === $user->id ||
                   $course->college->associate_dean_id === $user->id;
        }

        if ($user->position === 'department_chair') {
            return $user->department_id &&
                   $course->college_id === $user->college_id;
        }

        return $user->hasPermissionTo('delete courses');
    }

    public function restore(User $user, Course $course): bool
    {
        return $user->position === 'superadmin';
    }

    public function forceDelete(User $user, Course $course): bool
    {
        return $user->position === 'superadmin';
    }
}
