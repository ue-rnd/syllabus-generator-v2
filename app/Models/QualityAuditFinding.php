<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityAuditFinding extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_audit_id',
        'syllabus_id',
        'title',
        'description',
        'severity',
        'category',
        'evidence',
        'recommendation',
        'status',
        'assigned_to',
        'due_date',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'evidence' => 'array',
        'due_date' => 'date',
        'resolved_at' => 'datetime',
    ];

    public function qualityAudit()
    {
        return $this->belongsTo(QualityAudit::class);
    }

    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function actions()
    {
        return $this->hasMany(QualityAuditAction::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'resolved');
    }

    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'primary',
            'low' => 'success',
            'info' => 'info',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'danger',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'content' => 'blue',
            'structure' => 'green',
            'compliance' => 'purple',
            'quality' => 'orange',
            'documentation' => 'pink',
            'process' => 'indigo',
            default => 'gray',
        };
    }

    public function isOverdue(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               $this->status !== 'resolved';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function resolve(string $resolutionNotes): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $resolutionNotes,
        ]);
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        if (! $this->due_date) {
            return null;
        }

        return (int) now()->diffInDays($this->due_date, false);
    }

    public static function getSeverityOptions(): array
    {
        return [
            'critical' => 'Critical',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
            'info' => 'Informational',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ];
    }

    public static function getCategoryOptions(): array
    {
        return [
            'content' => 'Content Quality',
            'structure' => 'Structure & Format',
            'compliance' => 'Compliance Issues',
            'quality' => 'Quality Standards',
            'documentation' => 'Documentation',
            'process' => 'Process Issues',
        ];
    }
}
