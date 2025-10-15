<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QualityAuditFinding> $findings
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Syllabus> $syllabi
 */
class QualityAudit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'audit_type',
        'scope',
        'start_date',
        'end_date',
        'status',
        'auditor_id',
        'college_id',
        'department_id',
        'criteria',
        'summary',
        'recommendations',
        'follow_up_required',
        'follow_up_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'follow_up_date' => 'date',
        'criteria' => 'array',
        'follow_up_required' => 'boolean',
    ];

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function findings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(QualityAuditFinding::class);
    }

    public function actions()
    {
        return $this->hasMany(QualityAuditAction::class);
    }

    public function syllabi(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Syllabus::class, 'quality_audit_syllabi')
            ->withPivot(['audit_score', 'compliance_status', 'notes'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('audit_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('start_date', 'desc');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'planned' => 'gray',
            'in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }

    public function getAuditTypeColorAttribute(): string
    {
        return match ($this->audit_type) {
            'compliance' => 'primary',
            'quality_improvement' => 'success',
            'accreditation' => 'warning',
            'internal' => 'info',
            'external' => 'purple',
            default => 'gray',
        };
    }

    public function getScopeColorAttribute(): string
    {
        return match ($this->scope) {
            'institution' => 'purple',
            'college' => 'primary',
            'department' => 'success',
            'program' => 'warning',
            'course' => 'info',
            default => 'gray',
        };
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getTotalFindingsAttribute(): int
    {
        return $this->findings()->count();
    }

    public function getCriticalFindingsAttribute(): int
    {
        return $this->findings()->where('severity', 'critical')->count();
    }

    public function getHighFindingsAttribute(): int
    {
        return $this->findings()->where('severity', 'high')->count();
    }

    public function getPendingActionsAttribute(): int
    {
        return $this->actions()->where('status', 'pending')->count();
    }

    public function getCompletedActionsAttribute(): int
    {
        return $this->actions()->where('status', 'completed')->count();
    }

    public function calculateOverallScore(): float
    {
        $auditedSyllabi = $this->syllabi;

        if ($auditedSyllabi->isEmpty()) {
            return 0;
        }

        $totalScore = $auditedSyllabi->sum('pivot.audit_score');

        return round($totalScore / $auditedSyllabi->count(), 2);
    }

    public function getComplianceRate(): float
    {
        $auditedSyllabi = $this->syllabi;

        if ($auditedSyllabi->isEmpty()) {
            return 0;
        }

        $compliantCount = $auditedSyllabi->where('pivot.compliance_status', 'compliant')->count();

        return round(($compliantCount / $auditedSyllabi->count()) * 100, 1);
    }

    public static function getStatusOptions(): array
    {
        return [
            'planned' => 'Planned',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    public static function getAuditTypeOptions(): array
    {
        return [
            'compliance' => 'Compliance Audit',
            'quality_improvement' => 'Quality Improvement',
            'accreditation' => 'Accreditation Review',
            'internal' => 'Internal Audit',
            'external' => 'External Audit',
        ];
    }

    public static function getScopeOptions(): array
    {
        return [
            'institution' => 'Institution-wide',
            'college' => 'College Level',
            'department' => 'Department Level',
            'program' => 'Program Level',
            'course' => 'Course Level',
        ];
    }
}
