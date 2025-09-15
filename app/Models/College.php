<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class College extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'mission',
        'vision',
        'core_values',
        'objectives',
        'is_active',
        'sort_order',
        'logo_path',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'objectives' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope a query to only include active colleges.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the college's logo URL.
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    /**
     * Check if the college is active.
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get all departments belonging to this college.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get all courses belonging to this college.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get all programs through departments.
     */
    public function programs()
    {
        return $this->hasManyThrough(Program::class, Department::class);
    }

    /**
     * Get all users belonging to this college.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all syllabi belonging to this college.
     */
    public function syllabi()
    {
        return $this->hasMany(Syllabus::class);
    }
}
