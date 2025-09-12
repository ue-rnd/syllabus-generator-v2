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
        'outcomes',
        'is_active',
        'sort_order',
        'college_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'outcomes' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'college_id' => 'integer',
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
}
