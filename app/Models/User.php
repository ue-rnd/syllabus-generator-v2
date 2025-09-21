<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'lastname',
        'firstname',
        'middlename',
        'role',
        'position',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if ($user->firstname && $user->lastname) {
                $user->name = trim($user->firstname.' '.$user->lastname);
            }
        });

        static::updating(function ($user) {
            if ($user->firstname && $user->lastname) {
                $user->name = trim($user->firstname.' '.$user->lastname);
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        if ($this->firstname && $this->lastname) {
            return Str::substr($this->firstname, 0, 1).Str::substr($this->lastname, 0, 1);
        }

        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute(): string
    {
        if ($this->firstname && $this->lastname) {
            $name = $this->firstname;
            $name .= ' '.$this->lastname;

            return $name;
        }

        return $this->attributes['name'] ?? '';
    }

    /**
     * Get the user's name (computed attribute for Filament compatibility)
     */
    public function getNameAttribute(): string
    {
        // If we have structured names, use them; otherwise use the raw name attribute
        if ($this->firstname && $this->lastname) {
            return $this->getFullNameAttribute();
        }

        return $this->attributes['name'] ?? '';
    }

    /**
     * Get the user's display name (first name + last name)
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->firstname && $this->lastname) {
            return $this->firstname.' '.$this->lastname;
        }

        return $this->name;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if user is a superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->position === 'superadmin';
    }

    /**
     * Check if user is a dean
     */
    public function isDean(): bool
    {
        return $this->position === 'dean';
    }

    /**
     * Check if user is an associate dean
     */
    public function isAssociateDean(): bool
    {
        return $this->position === 'associate_dean';
    }

    /**
     * Check if user is a department chair
     */
    public function isDepartmentChair(): bool
    {
        return $this->position === 'department_chair';
    }

    /**
     * Check if user is faculty
     */
    public function isFaculty(): bool
    {
        return $this->position === 'faculty';
    }

    /**
     * Get user's primary role name
     */
    public function getPrimaryRoleAttribute(): string
    {
        return $this->position;

        // $roles = $this->roles->pluck('name');

        // // Priority order: superadmin > admin > faculty
        // if ($roles->contains('superadmin')) return 'superadmin';
        // if ($roles->contains('admin')) return 'admin';
        // if ($roles->contains('faculty')) return 'faculty';

        // return $roles->first() ?? 'No Role';
    }

    /**
     * Update last login information
     */
    public function updateLastLogin(?string $ip = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }

    /**
     * Scope a query to search users by name (first, last, middle, or full name)
     */
    public function scopeSearchByName($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('firstname', 'like', "%{$search}%")
                ->orWhere('lastname', 'like', "%{$search}%")
                ->orWhere('middlename', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"])
                ->orWhereRaw("CONCAT(firstname, ' ', middlename, ' ', lastname) LIKE ?", ["%{$search}%"])
                ->orWhereRaw("CONCAT(lastname, ', ', firstname) LIKE ?", ["%{$search}%"]);
        });
    }

    /**
     * Scope a query to only include active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the college that the user belongs to
     */
    public function college()
    {
        return $this->belongsTo(College::class);
    }

    /**
     * Get the college that the user belongs to
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Check if user is a dean of a specific college
     */
    public function isDeanOf(College $college): bool
    {
        return $this->position === 'dean' && $college->dean_id === $this->id;
    }

    /**
     * Check if user is an associate dean of a specific college
     */
    public function isAssociateDeanOf(College $college): bool
    {
        return $this->position === 'associate_dean' && $college->associate_dean_id === $this->id;
    }

    /**
     * Check if user is a department chair of a specific department
     */
    public function isDepartmentChairOf(Department $department): bool
    {
        return $this->position === 'department_chair' && $department->department_chair_id === $this->id;
    }

    /**
     * Get all colleges this user has administrative access to
     */
    public function getAccessibleColleges()
    {
        if ($this->position === 'superadmin') {
            return College::query();
        }

        if (in_array($this->position, ['dean', 'associate_dean'])) {
            return College::where(function ($query) {
                $query->where('dean_id', $this->id)
                    ->orWhere('associate_dean_id', $this->id);

                if ($this->college_id) {
                    $query->orWhere('id', $this->college_id);
                }
            });
        }

        return College::whereRaw('0 = 1');
    }

    /**
     * Get all departments this user has administrative access to
     */
    public function getAccessibleDepartments()
    {
        if ($this->position === 'superadmin') {
            return Department::query();
        }

        if (in_array($this->position, ['dean', 'associate_dean'])) {
            $collegeIds = $this->getAccessibleColleges()->pluck('id');

            return Department::whereIn('college_id', $collegeIds);
        }

        if ($this->position === 'department_chair') {
            return Department::where('department_chair_id', $this->id);
        }

        return Department::whereRaw('0 = 1');
    }

    /**
     * Get all programs this user has administrative access to
     */
    public function getAccessiblePrograms()
    {
        if ($this->position === 'superadmin') {
            return Program::query();
        }

        $departmentIds = $this->getAccessibleDepartments()->pluck('id');

        return Program::whereIn('department_id', $departmentIds);
    }

    /**
     * Get all courses this user has administrative access to
     */
    public function getAccessibleCourses()
    {
        if ($this->position === 'superadmin') {
            return Course::query();
        }

        $collegeIds = $this->getAccessibleColleges()->pluck('id');

        return Course::whereIn('college_id', $collegeIds);
    }

    /**
     * Get all syllabi this user has access to
     */
    public function getAccessibleSyllabi()
    {
        if ($this->position === 'superadmin') {
            return Syllabus::query();
        }

        if (in_array($this->position, ['dean', 'associate_dean', 'department_chair'])) {
            $collegeIds = $this->getAccessibleColleges()->pluck('id');

            return Syllabus::whereIn('college_id', $collegeIds);
        }

        if ($this->position === 'faculty') {
            return Syllabus::where('created_by', $this->id);
        }

        return Syllabus::query();
    }
}
