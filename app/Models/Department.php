<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'sort_order',
        'college_id',
        'department_chair_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'college_id' => 'integer',
        'department_chair_id' => 'integer',
    ];

    /**
     * Scope a query to only include active departments.
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
     * Check if the department is active.
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get the college that owns this department.
     */
    public function college()
    {
        return $this->belongsTo(College::class);
    }

    /**
     * Get all programs belonging to this department.
     */
    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get the department chair.
     */
    public function departmentChair()
    {
        return $this->belongsTo(User::class, 'department_chair_id');
    }
}
