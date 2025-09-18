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
        'objectives_text',
        'is_active',
        'sort_order',
        'logo_path',
        'dean_id',
        'associate_dean_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'objectives' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'dean_id' => 'integer',
        'associate_dean_id' => 'integer',
    ];

    /**
     * Get the objectives as a formatted string for display.
     */
    public function getObjectivesTextAttribute()
    {
        if (is_array($this->objectives)) {
            return implode("\n", $this->objectives);
        }
        return $this->objectives;
    }

    /**
     * Set the objectives from a text string.
     */
    public function setObjectivesTextAttribute($value)
    {
        if (is_string($value)) {
            $this->objectives = array_filter(array_map('trim', explode("\n", $value)));
        } else {
            $this->objectives = $value;
        }
    }

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

    /**
     * Get the dean of this college.
     */
    public function dean()
    {
        return $this->belongsTo(User::class, 'dean_id');
    }

    /**
     * Get the associate dean of this college.
     */
    public function associateDean()
    {
        return $this->belongsTo(User::class, 'associate_dean_id');
    }
}
