<?php

namespace App\Providers;

use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\Program;
use App\Models\Syllabus;
use App\Policies\CollegePolicy;
use App\Policies\CoursePolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\ProgramPolicy;
use App\Policies\SyllabusPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        College::class => CollegePolicy::class,
        Department::class => DepartmentPolicy::class,
        Program::class => ProgramPolicy::class,
        Course::class => CoursePolicy::class,
        Syllabus::class => SyllabusPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('access-settings', function ($user) {
            return $user->can('manage system settings');
        });
    }
}
