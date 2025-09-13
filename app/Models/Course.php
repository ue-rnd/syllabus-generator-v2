<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'credit_units_lecture',
        'credit_units_laboratory',
        'course_type',
        'prerequisite_courses',
        'outcomes',
        'is_active',
        'sort_order',
        'college_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'prerequisite_courses' => 'array',
        'outcomes' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'college_id' => 'integer',
        'credit_units_lecture' => 'decimal:1',
        'credit_units_laboratory' => 'decimal:1',
    ];

    /**
     * Scope a query to only include active courses.
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
     * Check if the course is active.
     */
    public function isActive()
    {
        return $this->is_active;
    }
    
    /**
     * Get the college that owns this course.
     */
    public function college()
    {
        return $this->belongsTo(College::class);
    }

    /**
     * Get the programs associated with this course.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'course_program');
    }

    /**
     * Get all syllabi for this course.
     */
    public function syllabi()
    {
        return $this->hasMany(Syllabus::class);
    }

    /**
     * Get the active (latest) syllabus for this course.
     */
    public function activeSyllabus()
    {
        return $this->hasOne(Syllabus::class)->latest('created_at');
    }

    /**
     * Get the latest syllabus for this course.
     */
    public function latestSyllabus()
    {
        return $this->activeSyllabus();
    }

    /**
     * Get prerequisite courses.
     */
    public function prerequisiteCourses()
    {
        if (empty($this->prerequisite_courses)) {
            return collect();
        }
        
        return Course::whereIn('id', $this->prerequisite_courses)->get();
    }

    /**
     * Get courses that have this course as a prerequisite.
     */
    public function dependentCourses()
    {
        return Course::whereJsonContains('prerequisite_courses', $this->id)->get();
    }

    /**
     * Get total credit units.
     */
    public function getTotalCreditUnitsAttribute()
    {
        return $this->credit_units_lecture + $this->credit_units_laboratory;
    }

    /**
     * Check if course has prerequisites.
     */
    public function hasPrerequisites()
    {
        return !empty($this->prerequisite_courses);
    }

    /**
     * Check if course has an active (latest) syllabus.
     */
    public function hasActiveSyllabus()
    {
        return $this->syllabi()->exists();
    }
}
