<?php

namespace App\Policies;

use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SyllabusPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Syllabus $syllabus): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function createForCourse(User $user, $courseId): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        $course = \App\Models\Course::find($courseId);
        if (! $course) {
            return false;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            $accessibleCollegeIds = $user->getAccessibleColleges()->pluck('id')->toArray();

            return in_array($course->college_id, $accessibleCollegeIds);
        }

        if ($user->position === 'department_chair') {
            // Department chairs can create syllabi for courses in their college
            return $user->college_id === $course->college_id;
        }

        // Faculty can create syllabi for any course (general permission)
        return true;
    }

    public function update(User $user, Syllabus $syllabus): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            return $user->college_id === $syllabus->college_id ||
                   $syllabus->college->dean_id === $user->id ||
                   $syllabus->college->associate_dean_id === $user->id;
        }

        if ($user->position === 'department_chair') {
            return $user->department_id &&
                   $syllabus->college_id === $user->college_id;
        }

        if ($user->position === 'faculty') {
            return $syllabus->created_by === $user->id;
        }

        return $user->hasPermissionTo('edit syllabi');
    }

    public function delete(User $user, Syllabus $syllabus): bool
    {
        if ($user->position === 'superadmin') {
            return true;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            return $user->college_id === $syllabus->college_id ||
                   $syllabus->college->dean_id === $user->id ||
                   $syllabus->college->associate_dean_id === $user->id;
        }

        if ($user->position === 'department_chair') {
            return $user->department_id &&
                   $syllabus->college_id === $user->college_id;
        }

        if ($user->position === 'faculty') {
            return $syllabus->created_by === $user->id;
        }

        return $user->hasPermissionTo('delete syllabi');
    }

    public function restore(User $user, Syllabus $syllabus): bool
    {
        return $user->position === 'superadmin';
    }

    public function forceDelete(User $user, Syllabus $syllabus): bool
    {
        return $user->position === 'superadmin';
    }
}
