<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\Syllabus $syllabus
 * @property-read \App\Models\QualityChecklist $qualityChecklist
 */
class SyllabusQualityCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_id',
        'quality_checklist_id',
        'checked_by',
        'checked_at',
        'overall_score',
        'status',
        'item_results',
        'notes',
        'auto_generated',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'overall_score' => 'decimal:2',
        'item_results' => 'array',
        'auto_generated' => 'boolean',
    ];

    public function syllabus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Syllabus::class);
    }

    public function qualityChecklist(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(QualityChecklist::class);
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePassed($query)
    {
        return $query->where('status', 'passed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function runQualityCheck(): void
    {
        $checklist = $this->qualityChecklist;
        $syllabus = $this->syllabus;

        if (! $checklist || ! $syllabus) {
            return;
        }

        $results = [];
        $totalScore = 0;
        $totalWeight = 0;

        foreach ($checklist->activeItems as $item) {
            $result = $item->validateSyllabus($syllabus);
            $results[] = $result;

            $weight = $item->weight ?? 1;
            $totalScore += $result['score'] * $weight;
            $totalWeight += $weight;
        }

        $overallScore = $totalWeight > 0 ? round($totalScore / $totalWeight, 2) : 0;
        $status = $this->determineStatus($overallScore, $results);

        $this->update([
            'item_results' => $results,
            'overall_score' => $overallScore,
            'status' => $status,
            'checked_at' => now(),
            'auto_generated' => true,
        ]);
    }

    private function determineStatus(float $overallScore, array $results): string
    {
        // Check if any mandatory items failed
        $checklist = $this->qualityChecklist;
        $mandatoryItemIds = $checklist->items()->mandatory()->pluck('id')->toArray();

        foreach ($results as $result) {
            if (in_array($result['item_id'], $mandatoryItemIds) && $result['status'] === 'failed') {
                return 'failed';
            }
        }

        // Determine status based on overall score
        if ($overallScore >= 90) {
            return 'passed';
        } elseif ($overallScore >= 70) {
            return 'requires_improvement';
        } else {
            return 'failed';
        }
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'passed' => 'success',
            'requires_improvement' => 'warning',
            'failed' => 'danger',
            'in_progress' => 'info',
            'completed' => 'primary',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'passed' => 'Passed',
            'requires_improvement' => 'Requires Improvement',
            'failed' => 'Failed',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            default => 'Unknown',
        };
    }

    public function getPassedItemsCountAttribute(): int
    {
        return collect($this->item_results ?? [])
            ->where('status', 'passed')
            ->count();
    }

    public function getFailedItemsCountAttribute(): int
    {
        return collect($this->item_results ?? [])
            ->where('status', 'failed')
            ->count();
    }

    public function getTotalItemsCountAttribute(): int
    {
        return count($this->item_results ?? []);
    }

    public function getCompletionRateAttribute(): float
    {
        $total = $this->total_items_count;

        return $total > 0 ? round(($this->passed_items_count / $total) * 100, 1) : 0;
    }

    public static function getStatusOptions(): array
    {
        return [
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'passed' => 'Passed',
            'requires_improvement' => 'Requires Improvement',
            'failed' => 'Failed',
        ];
    }

    public static function createForSyllabus(Syllabus $syllabus, QualityChecklist $checklist, ?User $checker = null): self
    {
        return self::create([
            'syllabus_id' => $syllabus->id,
            'quality_checklist_id' => $checklist->id,
            'checked_by' => $checker?->id,
            'status' => 'in_progress',
            'auto_generated' => $checker === null,
        ]);
    }
}
