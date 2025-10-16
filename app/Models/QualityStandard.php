<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QualityStandard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'category',
        'criteria',
        'minimum_score',
        'weight',
        'is_mandatory',
        'is_active',
        'institution_id',
        'college_id',
        'department_id',
        'sort_order',
    ];

    protected $casts = [
        'criteria' => 'array',
        'minimum_score' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function compliances()
    {
        return $this->hasMany(StandardsCompliance::class);
    }

    public function getTypeColorAttribute(): string
    {
        $type = (string) $this->attributes['type'];

        return match ($type) {
            'institutional' => 'primary',
            'accreditation' => 'success',
            'departmental' => 'warning',
            'program' => 'info',
            'course' => 'purple',
            default => 'gray',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        $category = (string) $this->attributes['category'];

        return match ($category) {
            'content' => 'blue',
            'structure' => 'green',
            'assessment' => 'orange',
            'learning_outcomes' => 'purple',
            'resources' => 'pink',
            'policies' => 'indigo',
            default => 'gray',
        };
    }

    public static function getTypeOptions(): array
    {
        return [
            'institutional' => 'Institutional',
            'accreditation' => 'Accreditation',
            'departmental' => 'Departmental',
            'program' => 'Program',
            'course' => 'Course',
        ];
    }

    public static function getCategoryOptions(): array
    {
        return [
            'content' => 'Content Quality',
            'structure' => 'Structure & Format',
            'assessment' => 'Assessment Methods',
            'learning_outcomes' => 'Learning Outcomes',
            'resources' => 'Resources & Materials',
            'policies' => 'Policies & Procedures',
        ];
    }
}
