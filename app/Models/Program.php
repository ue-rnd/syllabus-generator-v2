<?php

namespace App\Models;

use App\Constants\ProgramConstants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'level',
        'code',
        'description',
        'outcomes',
        'objectives',
        'is_active',
        'sort_order',
        'department_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'department_id' => 'integer',
        'objectives' => 'array',
        'outcomes' => 'array',
    ];

    /**
     * Scope a query to only include active programs.
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
     * Scope a query to filter by level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Check if the program is active.
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get the human-readable level name.
     */
    public function getLevelNameAttribute()
    {
        return ProgramConstants::getLevelOptions()[$this->level] ?? $this->level;
    }

    /**
     * Get the department that owns this program.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the college through the department.
     */
    public function college()
    {
        return $this->hasOneThrough(College::class, Department::class, 'id', 'id', 'department_id', 'college_id');
    }

    /**
     * Get the courses associated with this program.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_program');
    }
}
