<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'report_type',
        'template_config',
        'default_filters',
        'default_scope',
        'output_format',
        'is_public',
        'is_system',
        'created_by',
        'college_id',
        'department_id',
    ];

    protected $casts = [
        'template_config' => 'array',
        'default_filters' => 'array',
        'is_public' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function customReports()
    {
        return $this->hasMany(CustomReport::class);
    }

    public function schedules()
    {
        return $this->hasMany(ReportSchedule::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeAccessibleBy($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('is_public', true)
                ->orWhere('created_by', $user->id)
                ->orWhere('college_id', $user->college_id)
                ->orWhere('department_id', $user->department_id);
        });
    }

    public function getReportTypeColorAttribute(): string
    {
        return match ($this->report_type) {
            'compliance' => 'primary',
            'quality' => 'success',
            'analytics' => 'warning',
            'audit' => 'info',
            'custom' => 'purple',
            default => 'gray',
        };
    }

    public function getOutputFormatColorAttribute(): string
    {
        return match ($this->output_format) {
            'pdf' => 'danger',
            'excel' => 'success',
            'csv' => 'warning',
            'json' => 'info',
            'html' => 'primary',
            default => 'gray',
        };
    }

    public function generateReport(array $parameters = []): CustomReport
    {
        $config = array_merge($this->template_config ?? [], $parameters);
        $filters = array_merge($this->default_filters ?? [], $parameters['filters'] ?? []);

        return CustomReport::create([
            'name' => $parameters['name'] ?? ($this->name . ' - ' . now()->format('Y-m-d H:i')),
            'description' => $parameters['description'] ?? $this->description,
            'report_template_id' => $this->id,
            'report_config' => $config,
            'filters' => $filters,
            'scope' => $parameters['scope'] ?? $this->default_scope,
            'scope_id' => $parameters['scope_id'] ?? null,
            'output_format' => $parameters['output_format'] ?? $this->output_format,
            'generated_by' => auth()->id(),
            'status' => 'pending',
        ]);
    }

    public function validateConfig(array $config): array
    {
        $errors = [];
        $requiredFields = $this->getRequiredConfigFields();

        foreach ($requiredFields as $field) {
            if (! isset($config[$field]) || empty($config[$field])) {
                $errors[] = "Required field '{$field}' is missing";
            }
        }

        return $errors;
    }

    public function getRequiredConfigFields(): array
    {
        return match ($this->report_type) {
            'compliance' => ['standards', 'period', 'scope'],
            'quality' => ['checklists', 'period', 'scope'],
            'analytics' => ['metrics', 'period', 'scope'],
            'audit' => ['audit_criteria', 'period', 'scope'],
            'custom' => ['data_sources', 'aggregations'],
            default => [],
        };
    }

    public function getAvailableFields(): array
    {
        return match ($this->report_type) {
            'compliance' => [
                'syllabus.name' => 'Syllabus Name',
                'syllabus.status' => 'Syllabus Status',
                'course.name' => 'Course Name',
                'course.code' => 'Course Code',
                'compliance.status' => 'Compliance Status',
                'compliance.score' => 'Compliance Score',
                'standard.name' => 'Standard Name',
                'standard.type' => 'Standard Type',
            ],
            'quality' => [
                'syllabus.name' => 'Syllabus Name',
                'quality_check.score' => 'Quality Score',
                'quality_check.status' => 'Check Status',
                'checklist.name' => 'Checklist Name',
                'checklist.type' => 'Checklist Type',
            ],
            'analytics' => [
                'metric.name' => 'Metric Name',
                'metric.value' => 'Metric Value',
                'metric.period' => 'Period',
                'metric.scope' => 'Scope',
            ],
            'audit' => [
                'audit.name' => 'Audit Name',
                'audit.type' => 'Audit Type',
                'finding.severity' => 'Finding Severity',
                'finding.category' => 'Finding Category',
                'action.status' => 'Action Status',
            ],
            default => [],
        };
    }

    public function getDefaultTemplate(): array
    {
        return match ($this->report_type) {
            'compliance' => [
                'title' => 'Compliance Report',
                'sections' => [
                    'executive_summary',
                    'compliance_overview',
                    'standards_analysis',
                    'recommendations',
                ],
                'charts' => ['compliance_rate', 'standards_breakdown'],
                'tables' => ['syllabus_compliance', 'non_compliant_items'],
            ],
            'quality' => [
                'title' => 'Quality Assessment Report',
                'sections' => [
                    'quality_overview',
                    'checklist_results',
                    'improvement_areas',
                ],
                'charts' => ['quality_scores', 'checklist_performance'],
                'tables' => ['quality_checks', 'failed_items'],
            ],
            'analytics' => [
                'title' => 'Analytics Dashboard Report',
                'sections' => [
                    'key_metrics',
                    'trends_analysis',
                    'performance_indicators',
                ],
                'charts' => ['metric_trends', 'performance_comparison'],
                'tables' => ['metric_summary', 'trend_data'],
            ],
            default => [
                'title' => 'Custom Report',
                'sections' => ['summary'],
                'charts' => [],
                'tables' => [],
            ],
        };
    }

    public static function getReportTypeOptions(): array
    {
        return [
            'compliance' => 'Compliance Report',
            'quality' => 'Quality Assessment',
            'analytics' => 'Analytics Dashboard',
            'audit' => 'Audit Report',
            'custom' => 'Custom Report',
        ];
    }

    public static function getOutputFormatOptions(): array
    {
        return [
            'pdf' => 'PDF Document',
            'excel' => 'Excel Spreadsheet',
            'csv' => 'CSV File',
            'json' => 'JSON Data',
            'html' => 'HTML Report',
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

    public static function createSystemTemplates(): void
    {
        $systemTemplates = [
            [
                'name' => 'Standard Compliance Report',
                'description' => 'Comprehensive compliance assessment report',
                'report_type' => 'compliance',
                'template_config' => [
                    'title' => 'Compliance Assessment Report',
                    'sections' => ['executive_summary', 'compliance_overview', 'standards_analysis'],
                    'include_charts' => true,
                    'include_recommendations' => true,
                ],
                'default_scope' => 'college',
                'output_format' => 'pdf',
                'is_public' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Quality Checklist Report',
                'description' => 'Quality assessment based on checklists',
                'report_type' => 'quality',
                'template_config' => [
                    'title' => 'Quality Assessment Report',
                    'sections' => ['quality_overview', 'checklist_results'],
                    'include_item_details' => true,
                ],
                'default_scope' => 'department',
                'output_format' => 'excel',
                'is_public' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Monthly Analytics Summary',
                'description' => 'Monthly performance metrics and trends',
                'report_type' => 'analytics',
                'template_config' => [
                    'title' => 'Monthly Analytics Summary',
                    'sections' => ['key_metrics', 'trends_analysis'],
                    'period' => 'monthly',
                ],
                'default_scope' => 'institution',
                'output_format' => 'pdf',
                'is_public' => true,
                'is_system' => true,
            ],
        ];

        foreach ($systemTemplates as $template) {
            self::firstOrCreate(
                ['name' => $template['name'], 'is_system' => true],
                $template
            );
        }
    }
}
