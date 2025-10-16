<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Carbon|null $due_date
 * @property Carbon|null $completion_date
 */
class QualityAuditAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_audit_id',
        'quality_audit_finding_id',
        'title',
        'description',
        'action_type',
        'priority',
        'assigned_to',
        'due_date',
        'status',
        'progress_percentage',
        'completion_date',
        'notes',
        'evidence',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completion_date' => 'datetime',
        'progress_percentage' => 'integer',
        'evidence' => 'array',
    ];

    public function qualityAudit()
    {
        return $this->belongsTo(QualityAudit::class);
    }

    public function qualityAuditFinding()
    {
        return $this->belongsTo(QualityAuditFinding::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'completed');
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function getPriorityColorAttribute(): string
    {
        $priority = (string) $this->attributes['priority'];

        return match ($priority) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'primary',
            'low' => 'success',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        $status = (string) $this->attributes['status'];

        return match ($status) {
            'pending' => 'gray',
            'in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'on_hold' => 'info',
            default => 'gray',
        };
    }

    public function getActionTypeColorAttribute(): string
    {
        $actionType = (string) $this->attributes['action_type'];

        return match ($actionType) {
            'corrective' => 'danger',
            'preventive' => 'warning',
            'improvement' => 'success',
            'training' => 'info',
            'documentation' => 'primary',
            'process_change' => 'purple',
            default => 'gray',
        };
    }

    public function isOverdue(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               $this->status !== 'completed';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function complete(?string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'progress_percentage' => 100,
            'completion_date' => now(),
            'notes' => $notes ?: $this->notes,
        ]);
    }

    public function updateProgress(int $percentage, ?string $notes = null): void
    {
        $status = $this->status;
        if ($percentage >= 100) {
            $status = 'completed';
        } elseif ($percentage > 0 && $this->status === 'pending') {
            $status = 'in_progress';
        }

        $this->update([
            'progress_percentage' => min(100, max(0, $percentage)),
            'status' => $status,
            'notes' => $notes ?: $this->notes,
            'completion_date' => $percentage >= 100 ? now() : null,
        ]);
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        if (! $this->due_date) {
            return null;
        }

        return (int) now()->diffInDays($this->due_date, false);
    }

    public static function getPriorityOptions(): array
    {
        return [
            'critical' => 'Critical',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'on_hold' => 'On Hold',
        ];
    }

    public static function getActionTypeOptions(): array
    {
        return [
            'corrective' => 'Corrective Action',
            'preventive' => 'Preventive Action',
            'improvement' => 'Improvement Action',
            'training' => 'Training Required',
            'documentation' => 'Documentation Update',
            'process_change' => 'Process Change',
        ];
    }
}
