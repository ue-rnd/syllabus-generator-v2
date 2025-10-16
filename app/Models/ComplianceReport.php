<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplianceReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'report_type',
        'scope',
        'scope_id',
        'period_start',
        'period_end',
        'filters',
        'generated_by',
        'generated_at',
        'status',
        'file_path',
        'file_format',
        'summary',
        'total_syllabi',
        'compliant_syllabi',
        'non_compliant_syllabi',
        'compliance_rate',
        'auto_generated',
        'schedule_id',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'filters' => 'array',
        'generated_at' => 'datetime',
        'summary' => 'array',
        'total_syllabi' => 'integer',
        'compliant_syllabi' => 'integer',
        'non_compliant_syllabi' => 'integer',
        'compliance_rate' => 'decimal:2',
        'auto_generated' => 'boolean',
    ];

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function schedule()
    {
        return $this->belongsTo(ReportSchedule::class, 'schedule_id');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByScope($query, string $scope, ?int $scopeId = null)
    {
        $query = $query->where('scope', $scope);
        if ($scopeId) {
            $query->where('scope_id', $scopeId);
        }

        return $query;
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('period_start', [$startDate, $endDate])
            ->orWhereBetween('period_end', [$startDate, $endDate]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('generated_at', '>=', now()->subDays($days));
    }

    public function getReportTypeColorAttribute(): string
    {
        $reportType = (string) $this->attributes['report_type'];

        return match ($reportType) {
            'compliance_summary' => 'primary',
            'quality_audit' => 'success',
            'standards_assessment' => 'warning',
            'progress_tracking' => 'info',
            'custom' => 'purple',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        $status = (string) $this->attributes['status'];

        return match ($status) {
            'pending' => 'gray',
            'generating' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'orange',
            default => 'gray',
        };
    }

    public function getScopeColorAttribute(): string
    {
        $scope = (string) $this->attributes['scope'];

        return match ($scope) {
            'institution' => 'purple',
            'college' => 'primary',
            'department' => 'success',
            'program' => 'warning',
            'course' => 'info',
            default => 'gray',
        };
    }

    public function getComplianceRateColorAttribute(): string
    {
        if ($this->compliance_rate >= 90) {
            return 'success';
        }
        if ($this->compliance_rate >= 75) {
            return 'warning';
        }
        if ($this->compliance_rate >= 50) {
            return 'orange';
        }

        return 'danger';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function generateReport(): void
    {
        $this->update(['status' => 'generating']);

        try {
            $data = $this->collectData();
            $summary = $this->generateSummary($data);

            $this->update([
                'summary' => $summary,
                'total_syllabi' => $data['total_syllabi'],
                'compliant_syllabi' => $data['compliant_syllabi'],
                'non_compliant_syllabi' => $data['non_compliant_syllabi'],
                'compliance_rate' => $data['compliance_rate'],
                'status' => 'completed',
                'generated_at' => now(),
            ]);

            if ($this->file_format) {
                $this->exportToFile();
            }

        } catch (\Exception $e) {
            $this->update([
                'status' => 'failed',
                'summary' => ['error' => $e->getMessage()],
            ]);
        }
    }

    private function collectData(): array
    {
        $query = $this->buildSyllabiQuery();
        $syllabi = $query->get();
        $totalSyllabi = $syllabi->count();

        $complianceData = $this->analyzeCompliance($syllabi);

        return [
            'total_syllabi' => $totalSyllabi,
            'compliant_syllabi' => $complianceData['compliant'],
            'non_compliant_syllabi' => $complianceData['non_compliant'],
            'compliance_rate' => $totalSyllabi > 0 ? round(($complianceData['compliant'] / $totalSyllabi) * 100, 2) : 0,
            'syllabi' => $syllabi,
            'compliance_details' => $complianceData['details'],
        ];
    }

    private function buildSyllabiQuery()
    {
        $query = Syllabus::query();

        // Apply scope filters
        if ($this->scope === 'college' && $this->scope_id) {
            $query->whereHas('course.college', fn ($q) => $q->where('id', $this->scope_id));
        } elseif ($this->scope === 'department' && $this->scope_id) {
            $query->whereHas('course.college.departments', fn ($q) => $q->where('id', $this->scope_id));
        }

        // Apply period filters
        if ($this->period_start && $this->period_end) {
            $query->whereBetween('created_at', [$this->period_start, $this->period_end]);
        }

        // Apply custom filters
        if ($this->filters) {
            foreach ($this->filters as $field => $value) {
                if (! empty($value)) {
                    $query->where($field, $value);
                }
            }
        }

        return $query->with(['course', 'standardsCompliances.qualityStandard']);
    }

    private function analyzeCompliance($syllabi): array
    {
        $compliant = 0;
        $nonCompliant = 0;
        $details = [];

        foreach ($syllabi as $syllabus) {
            $syllabusCompliance = $this->assessSyllabusCompliance($syllabus);
            if ($syllabusCompliance['is_compliant']) {
                $compliant++;
            } else {
                $nonCompliant++;
            }
            $details[] = $syllabusCompliance;
        }

        return [
            'compliant' => $compliant,
            'non_compliant' => $nonCompliant,
            'details' => $details,
        ];
    }

    private function assessSyllabusCompliance(Syllabus $syllabus): array
    {
        $compliances = $syllabus->standardsCompliances;
        $totalStandards = $compliances->count();
        $compliantStandards = $compliances->where('compliance_status', 'compliant')->count();

        $complianceRate = $totalStandards > 0 ? ($compliantStandards / $totalStandards) * 100 : 0;
        $isCompliant = $complianceRate >= 80; // 80% threshold

        return [
            'syllabus_id' => $syllabus->id,
            'syllabus_name' => $syllabus->name,
            'course_name' => $syllabus->course->name ?? 'Unknown',
            'total_standards' => $totalStandards,
            'compliant_standards' => $compliantStandards,
            'compliance_rate' => round($complianceRate, 2),
            'is_compliant' => $isCompliant,
            'non_compliant_standards' => $compliances->whereNotIn('compliance_status', ['compliant'])->map(function ($compliance) {
                return [
                    'standard_name' => $compliance->qualityStandard->name ?? 'Unknown',
                    'status' => $compliance->compliance_status,
                    'notes' => $compliance->notes,
                ];
            })->toArray(),
        ];
    }

    private function generateSummary(array $data): array
    {
        $summary = [
            'overview' => [
                'total_syllabi' => $data['total_syllabi'],
                'compliant_syllabi' => $data['compliant_syllabi'],
                'non_compliant_syllabi' => $data['non_compliant_syllabi'],
                'compliance_rate' => $data['compliance_rate'],
            ],
            'scope' => [
                'type' => $this->scope,
                'id' => $this->scope_id,
                'period' => [
                    'start' => $this->period_start?->format('Y-m-d'),
                    'end' => $this->period_end?->format('Y-m-d'),
                ],
            ],
            'generated' => [
                'at' => now()->toISOString(),
                'by' => $this->generatedBy->name ?? 'System',
            ],
        ];

        if ($this->report_type === 'compliance_summary') {
            $summary['compliance_breakdown'] = $this->generateComplianceBreakdown($data);
        }

        return $summary;
    }

    private function generateComplianceBreakdown(array $data): array
    {
        $breakdown = [
            'by_status' => [],
            'by_standard' => [],
            'trends' => [],
        ];

        // Group by compliance status
        $statusCounts = collect($data['compliance_details'])->groupBy('is_compliant');
        $breakdown['by_status'] = [
            'compliant' => $statusCounts->get('1', collect())->count(),
            'non_compliant' => $statusCounts->get('0', collect())->count(),
        ];

        return $breakdown;
    }

    private function exportToFile(): void
    {
        // Implementation for file export would go here
        // Could generate PDF, Excel, CSV, etc.
        $filename = $this->generateFilename();
        $this->update(['file_path' => $filename]);
    }

    private function generateFilename(): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $extension = match ($this->file_format) {
            'pdf' => 'pdf',
            'excel' => 'xlsx',
            'csv' => 'csv',
            default => 'pdf',
        };

        return "reports/compliance_{$this->scope}_{$timestamp}.{$extension}";
    }

    public static function getReportTypeOptions(): array
    {
        return [
            'compliance_summary' => 'Compliance Summary',
            'quality_audit' => 'Quality Audit Report',
            'standards_assessment' => 'Standards Assessment',
            'progress_tracking' => 'Progress Tracking',
            'custom' => 'Custom Report',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'generating' => 'Generating',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
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

    public static function getFileFormatOptions(): array
    {
        return [
            'pdf' => 'PDF Document',
            'excel' => 'Excel Spreadsheet',
            'csv' => 'CSV File',
        ];
    }
}
