<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'report_template_id',
        'report_config',
        'filters',
        'scope',
        'scope_id',
        'period_start',
        'period_end',
        'output_format',
        'generated_by',
        'generated_at',
        'status',
        'file_path',
        'file_size',
        'execution_time',
        'record_count',
        'error_message',
        'is_scheduled',
        'schedule_id',
    ];

    protected $casts = [
        'report_config' => 'array',
        'filters' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
        'generated_at' => 'datetime',
        'file_size' => 'integer',
        'execution_time' => 'integer',
        'record_count' => 'integer',
        'is_scheduled' => 'boolean',
    ];

    public function reportTemplate()
    {
        return $this->belongsTo(ReportTemplate::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function schedule()
    {
        return $this->belongsTo(ReportSchedule::class, 'schedule_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByFormat($query, string $format)
    {
        return $query->where('output_format', $format);
    }

    public function scopeByScope($query, string $scope, ?int $scopeId = null)
    {
        $query = $query->where('scope', $scope);
        if ($scopeId) {
            $query->where('scope_id', $scopeId);
        }

        return $query;
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('generated_at', '>=', now()->subDays($days));
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'gray',
            'generating' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'orange',
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

    public function getFileSizeFormattedAttribute(): string
    {
        if (! $this->file_size) {
            return 'N/A';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf('%.2f %s', $bytes / pow(1024, $factor), $units[$factor]);
    }

    public function getExecutionTimeFormattedAttribute(): string
    {
        if (! $this->execution_time) {
            return 'N/A';
        }

        $seconds = $this->execution_time;
        if ($seconds < 60) {
            return "{$seconds}s";
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;

            return "{$minutes}m {$remainingSeconds}s";
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);

            return "{$hours}h {$minutes}m";
        }
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function generateReport(): void
    {
        $startTime = microtime(true);
        $this->update(['status' => 'generating', 'generated_at' => now()]);

        try {
            $data = $this->collectData();
            $output = $this->processData($data);
            $filePath = $this->saveOutput($output);

            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            $this->update([
                'status' => 'completed',
                'file_path' => $filePath,
                'file_size' => file_exists($filePath) ? filesize($filePath) : null,
                'execution_time' => $executionTime,
                'record_count' => is_array($data) ? count($data) : 1,
                'error_message' => null,
            ]);

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            $this->update([
                'status' => 'failed',
                'execution_time' => $executionTime,
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function collectData(): array
    {
        $reportType = $this->reportTemplate->report_type ?? 'custom';

        return match ($reportType) {
            'compliance' => $this->collectComplianceData(),
            'quality' => $this->collectQualityData(),
            'analytics' => $this->collectAnalyticsData(),
            'audit' => $this->collectAuditData(),
            default => $this->collectCustomData(),
        };
    }

    private function collectComplianceData(): array
    {
        $query = Syllabus::with(['standardsCompliances.qualityStandard', 'course']);

        $this->applyScopeFilters($query);
        $this->applyPeriodFilters($query);
        $this->applyCustomFilters($query);

        return $query->get()->map(function ($syllabus) {
            return [
                'syllabus_id' => $syllabus->id,
                'syllabus_name' => $syllabus->name,
                'course_name' => $syllabus->course->name ?? 'Unknown',
                'status' => $syllabus->status,
                'compliance_data' => $syllabus->standardsCompliances->map(function ($compliance) {
                    return [
                        'standard_name' => $compliance->qualityStandard->name ?? 'Unknown',
                        'standard_type' => $compliance->qualityStandard->type ?? 'Unknown',
                        'compliance_status' => $compliance->compliance_status,
                        'score' => $compliance->score,
                        'checked_at' => $compliance->checked_at?->format('Y-m-d'),
                    ];
                })->toArray(),
            ];
        })->toArray();
    }

    private function collectQualityData(): array
    {
        $query = SyllabusQualityCheck::with(['syllabus.course', 'qualityChecklist']);

        $this->applyScopeFilters($query, 'syllabus');
        $this->applyPeriodFilters($query, 'checked_at');
        $this->applyCustomFilters($query);

        return $query->get()->map(function ($check) {
            return [
                'syllabus_id' => $check->syllabus->id,
                'syllabus_name' => $check->syllabus->name,
                'course_name' => $check->syllabus->course->name ?? 'Unknown',
                'checklist_name' => $check->qualityChecklist->name,
                'checklist_type' => $check->qualityChecklist->type,
                'overall_score' => $check->overall_score,
                'status' => $check->status,
                'checked_at' => $check->checked_at?->format('Y-m-d'),
                'item_results' => $check->item_results ?? [],
            ];
        })->toArray();
    }

    private function collectAnalyticsData(): array
    {
        $query = SyllabusMetric::query();

        $this->applyScopeFilters($query);
        $this->applyPeriodFilters($query, 'calculated_at');
        $this->applyCustomFilters($query);

        return $query->get()->map(function ($metric) {
            return [
                'metric_name' => $metric->metric_name,
                'metric_value' => $metric->metric_value,
                'metric_type' => $metric->metric_type,
                'scope' => $metric->scope,
                'scope_id' => $metric->scope_id,
                'period_start' => $metric->period_start ? \Carbon\Carbon::parse($metric->period_start)->format('Y-m-d') : null,
                'period_end' => $metric->period_end ? \Carbon\Carbon::parse($metric->period_end)->format('Y-m-d') : null,
                'calculated_at' => $metric->calculated_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }

    private function collectAuditData(): array
    {
        $query = QualityAudit::with(['findings.actions', 'syllabi']);

        $this->applyScopeFilters($query);
        $this->applyPeriodFilters($query, 'start_date');
        $this->applyCustomFilters($query);

        return $query->get()->map(function ($audit) {
            return [
                'audit_id' => $audit->id,
                'audit_name' => $audit->name,
                'audit_type' => $audit->audit_type,
                'scope' => $audit->scope,
                'start_date' => $audit->start_date->format('Y-m-d'),
                'end_date' => $audit->end_date->format('Y-m-d'),
                'status' => $audit->status,
                'findings' => $audit->findings->map(function ($finding) {
                    return [
                        'title' => $finding->title,
                        'severity' => $finding->severity,
                        'category' => $finding->category,
                        'status' => $finding->status,
                        'actions_count' => $finding->actions->count(),
                    ];
                })->toArray(),
                'syllabi_count' => $audit->syllabi->count(),
            ];
        })->toArray();
    }

    private function collectCustomData(): array
    {
        // Implement custom data collection based on report_config
        return [];
    }

    private function applyScopeFilters($query, ?string $relation = null): void
    {
        if ($this->scope && $this->scope !== 'institution') {
            $scopeField = $relation ? "{$relation}.{$this->scope}_id" : "{$this->scope}_id";
            if ($this->scope_id) {
                $query->where($scopeField, $this->scope_id);
            }
        }
    }

    private function applyPeriodFilters($query, string $dateField = 'created_at'): void
    {
        if ($this->period_start && $this->period_end) {
            $query->whereBetween($dateField, [$this->period_start, $this->period_end]);
        } elseif ($this->period_start) {
            $query->where($dateField, '>=', $this->period_start);
        } elseif ($this->period_end) {
            $query->where($dateField, '<=', $this->period_end);
        }
    }

    private function applyCustomFilters($query): void
    {
        if ($this->filters) {
            foreach ($this->filters as $field => $value) {
                if (! empty($value)) {
                    if (is_array($value)) {
                        $query->whereIn($field, $value);
                    } else {
                        $query->where($field, $value);
                    }
                }
            }
        }
    }

    private function processData(array $data): array
    {
        $config = $this->report_config ?? [];

        // Apply any data transformations based on config
        if (isset($config['aggregations'])) {
            $data = $this->applyAggregations($data, $config['aggregations']);
        }

        if (isset($config['sorting'])) {
            $data = $this->applySorting($data, $config['sorting']);
        }

        if (isset($config['grouping'])) {
            $data = $this->applyGrouping($data, $config['grouping']);
        }

        return $data;
    }

    private function applyAggregations(array $data, array $aggregations): array
    {
        // Implementation for data aggregations
        return $data;
    }

    private function applySorting(array $data, array $sorting): array
    {
        // Implementation for data sorting
        return $data;
    }

    private function applyGrouping(array $data, array $grouping): array
    {
        // Implementation for data grouping
        return $data;
    }

    private function saveOutput(array $data): string
    {
        $filename = $this->generateFilename();
        $fullPath = storage_path("app/reports/{$filename}");

        // Ensure directory exists
        $directory = dirname($fullPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return match ($this->output_format) {
            'json' => $this->saveAsJson($data, $fullPath),
            'csv' => $this->saveAsCsv($data, $fullPath),
            'excel' => $this->saveAsExcel($data, $fullPath),
            'pdf' => $this->saveAsPdf($data, $fullPath),
            'html' => $this->saveAsHtml($data, $fullPath),
            default => $this->saveAsJson($data, $fullPath),
        };
    }

    private function generateFilename(): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $extension = match ($this->output_format) {
            'json' => 'json',
            'csv' => 'csv',
            'excel' => 'xlsx',
            'pdf' => 'pdf',
            'html' => 'html',
            default => 'json',
        };

        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->name);

        return "{$safeName}_{$timestamp}.{$extension}";
    }

    private function saveAsJson(array $data, string $path): string
    {
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

        return $path;
    }

    private function saveAsCsv(array $data, string $path): string
    {
        $file = fopen($path, 'w');

        if (! empty($data)) {
            // Write headers
            $headers = array_keys($data[0]);
            fputcsv($file, $headers);

            // Write data
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
        }

        fclose($file);

        return $path;
    }

    private function saveAsExcel(array $data, string $path): string
    {
        // Implementation would require PhpSpreadsheet
        // For now, fallback to CSV
        return $this->saveAsCsv($data, str_replace('.xlsx', '.csv', $path));
    }

    private function saveAsPdf(array $data, string $path): string
    {
        // Implementation would require a PDF library
        // For now, fallback to JSON
        return $this->saveAsJson($data, str_replace('.pdf', '.json', $path));
    }

    private function saveAsHtml(array $data, string $path): string
    {
        $html = $this->generateHtmlReport($data);
        file_put_contents($path, $html);

        return $path;
    }

    private function generateHtmlReport(array $data): string
    {
        $title = $this->name;
        $generatedAt = now()->format('Y-m-d H:i:s');
        $generatedBy = $this->generatedBy->name ?? 'Unknown';

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>{$title}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$title}</h1>
        <p>Generated on: {$generatedAt}</p>
        <p>Generated by: {$generatedBy}</p>
        <p>Records: {$this->record_count}</p>
    </div>
HTML;

        if (! empty($data)) {
            $html .= '<table><thead><tr>';

            // Headers
            $headers = array_keys($data[0]);
            foreach ($headers as $header) {
                $html .= '<th>' . htmlspecialchars($header) . '</th>';
            }
            $html .= '</tr></thead><tbody>';

            // Data rows
            foreach ($data as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) {
                    $html .= '<td>' . htmlspecialchars(is_array($cell) ? json_encode($cell) : $cell) . '</td>';
                }
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
        } else {
            $html .= '<p>No data available for this report.</p>';
        }

        $html .= '</body></html>';

        return $html;
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

    public static function getOutputFormatOptions(): array
    {
        return [
            'json' => 'JSON Data',
            'csv' => 'CSV File',
            'excel' => 'Excel Spreadsheet',
            'pdf' => 'PDF Document',
            'html' => 'HTML Report',
        ];
    }
}
