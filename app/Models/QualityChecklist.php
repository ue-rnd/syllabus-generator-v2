<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QualityChecklistItem> $activeItems
 */
class QualityChecklist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'is_default',
        'is_active',
        'college_id',
        'department_id',
        'created_by',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(QualityChecklistItem::class)->ordered();
    }

    public function activeItems()
    {
        return $this->hasMany(QualityChecklistItem::class)->active()->ordered();
    }

    public function syllabusChecks()
    {
        return $this->hasMany(SyllabusQualityCheck::class);
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'basic' => 'primary',
            'comprehensive' => 'success',
            'accreditation' => 'warning',
            'custom' => 'info',
            default => 'gray',
        };
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->activeItems()->count();
    }

    public function getCompletionRateForSyllabus(Syllabus $syllabus): array
    {
        $totalItems = $this->total_items;
        if ($totalItems === 0) {
            return ['rate' => 100, 'completed' => 0, 'total' => 0];
        }

        $syllabusCheck = $this->syllabusChecks()
            ->where('syllabus_id', $syllabus->id)
            ->first();

        if (! $syllabusCheck) {
            return ['rate' => 0, 'completed' => 0, 'total' => $totalItems];
        }

        $completedItems = collect($syllabusCheck->item_results ?? [])
            ->where('status', 'passed')
            ->count();

        $rate = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 1) : 0;

        return [
            'rate' => $rate,
            'completed' => $completedItems,
            'total' => $totalItems,
        ];
    }

    public static function getTypeOptions(): array
    {
        return [
            'basic' => 'Basic Quality Check',
            'comprehensive' => 'Comprehensive Review',
            'accreditation' => 'Accreditation Compliance',
            'custom' => 'Custom Checklist',
        ];
    }
}
