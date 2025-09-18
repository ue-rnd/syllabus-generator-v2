<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

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
                $user->name = trim($user->firstname . ' ' . $user->lastname);
            }
        });

        static::updating(function ($user) {
            if ($user->firstname && $user->lastname) {
                $user->name = trim($user->firstname . ' ' . $user->lastname);
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
            return Str::substr($this->firstname, 0, 1) . Str::substr($this->lastname, 0, 1);
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
            $name .= ' ' . $this->lastname;
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
            return $this->firstname . ' ' . $this->lastname;
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
        return $this->hasRole('superadmin');
    }

    /**
     * Check if user is an admin (college dean)
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is faculty
     */
    public function isFaculty(): bool
    {
        return $this->hasRole('faculty');
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
    public function updateLastLogin(string $ip = null): void
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
}
