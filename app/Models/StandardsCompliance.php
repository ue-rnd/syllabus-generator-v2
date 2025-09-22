<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandardsCompliance extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_id',
        'quality_standard_id',
        'compliance_status',
        'score',
        'notes',
        'checked_by',
        'checked_at',
        'evidence',
        'remediation_required',
        'remediation_notes',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'checked_at' => 'datetime',
        'evidence' => 'array',
        'remediation_required' => 'boolean',
    ];

    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    public function qualityStandard()
    {
        return $this->belongsTo(QualityStandard::class);
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function scopeCompliant($query)
    {
        return $query->where('compliance_status', 'compliant');
    }

    public function scopeNonCompliant($query)
    {
        return $query->where('compliance_status', 'non_compliant');
    }

    public function scopePartiallyCompliant($query)
    {
        return $query->where('compliance_status', 'partially_compliant');
    }

    public function scopeRequiresRemediation($query)
    {
        return $query->where('remediation_required', true);
    }

    public function getComplianceStatusColorAttribute(): string
    {
        return match ($this->compliance_status) {
            'compliant' => 'success',
            'partially_compliant' => 'warning',
            'non_compliant' => 'danger',
            'not_assessed' => 'gray',
            default => 'gray',
        };
    }

    public function getComplianceStatusLabelAttribute(): string
    {
        return match ($this->compliance_status) {
            'compliant' => 'Compliant',
            'partially_compliant' => 'Partially Compliant',
            'non_compliant' => 'Non-Compliant',
            'not_assessed' => 'Not Assessed',
            default => 'Unknown',
        };
    }

    public function isCompliant(): bool
    {
        return $this->compliance_status === 'compliant';
    }

    public function requiresRemediation(): bool
    {
        return $this->remediation_required;
    }

    public static function getComplianceStatusOptions(): array
    {
        return [
            'not_assessed' => 'Not Assessed',
            'compliant' => 'Compliant',
            'partially_compliant' => 'Partially Compliant',
            'non_compliant' => 'Non-Compliant',
        ];
    }
}