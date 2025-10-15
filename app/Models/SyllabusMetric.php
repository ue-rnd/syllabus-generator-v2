<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyllabusMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'metric_name',
        'metric_value',
        'metric_type',
        'period_start',
        'period_end',
        'scope',
        'scope_id',
        'metadata',
        'calculated_at',
    ];

    protected $casts = [
        'metric_value' => 'float',
        'period_start' => 'date',
        'period_end' => 'date',
        'metadata' => 'array',
        'calculated_at' => 'datetime',
    ];

    public function scopeByMetric($query, string $metricName)
    {
        return $query->where('metric_name', $metricName);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('metric_type', $type);
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

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('calculated_at', '>=', now()->subDays($days));
    }

    public function getMetricTypeColorAttribute(): string
    {
        return match ($this->metric_type) {
            'count' => 'primary',
            'percentage' => 'success',
            'score' => 'warning',
            'duration' => 'info',
            'rate' => 'purple',
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
            'user' => 'pink',
            default => 'gray',
        };
    }

    public function getFormattedValueAttribute(): string
    {
        return match ($this->metric_type) {
            'percentage' => number_format($this->metric_value, 1) . '%',
            'count' => number_format($this->metric_value),
            'score' => number_format($this->metric_value, 2),
            'duration' => $this->formatDuration($this->metric_value),
            'rate' => number_format($this->metric_value, 2) . '/day',
            default => (string) $this->metric_value,
        };
    }

    private function formatDuration(float $days): string
    {
        if ($days < 1) {
            return number_format($days * 24, 1) . ' hours';
        } elseif ($days < 7) {
            return number_format($days, 1) . ' days';
        } elseif ($days < 30) {
            return number_format($days / 7, 1) . ' weeks';
        } else {
            return number_format($days / 30, 1) . ' months';
        }
    }

    public static function getMetricTypeOptions(): array
    {
        return [
            'count' => 'Count',
            'percentage' => 'Percentage',
            'score' => 'Score',
            'duration' => 'Duration',
            'rate' => 'Rate',
        ];
    }

    public static function getScopeOptions(): array
    {
        return [
            'institution' => 'Institution',
            'college' => 'College',
            'department' => 'Department',
            'program' => 'Program',
            'course' => 'Course',
            'user' => 'User',
        ];
    }

    public static function calculateMetric(string $metricName, array $parameters = []): float
    {
        return match ($metricName) {
            'total_syllabi' => Syllabus::count(),
            'published_syllabi' => Syllabus::where('status', 'published')->count(),
            'draft_syllabi' => Syllabus::where('status', 'draft')->count(),
            'pending_approval_syllabi' => Syllabus::whereIn('status', ['pending_approval', 'dept_chair_review', 'assoc_dean_review', 'dean_review', 'qa_review'])->count(),
            'syllabi_compliance_rate' => self::calculateComplianceRate($parameters),
            'average_approval_time' => self::calculateAverageApprovalTime($parameters),
            'quality_score_average' => self::calculateAverageQualityScore($parameters),
            'suggestions_count' => SyllabusSuggestion::count(),
            'pending_suggestions' => SyllabusSuggestion::where('status', 'pending')->count(),
            default => 0,
        };
    }

    private static function calculateComplianceRate(array $parameters): float
    {
        $total = StandardsCompliance::count();
        if ($total === 0) {
            return 0;
        }

        $compliant = StandardsCompliance::where('compliance_status', 'compliant')->count();

        return round(($compliant / $total) * 100, 2);
    }

    private static function calculateAverageApprovalTime(array $parameters): float
    {
        $approvedSyllabi = Syllabus::whereNotNull('submitted_at')
            ->whereNotNull('dean_approved_at')
            ->get();

        if ($approvedSyllabi->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        foreach ($approvedSyllabi as $syllabus) {
            $totalDays += $syllabus->submitted_at->diffInDays($syllabus->dean_approved_at);
        }

        return round($totalDays / $approvedSyllabi->count(), 2);
    }

    private static function calculateAverageQualityScore(array $parameters): float
    {
        $qualityChecks = SyllabusQualityCheck::whereNotNull('overall_score')->get();

        if ($qualityChecks->isEmpty()) {
            return 0;
        }

        return round($qualityChecks->avg('overall_score'), 2);
    }

    public static function refreshMetric(string $metricName, array $parameters = []): self
    {
        $scope = $parameters['scope'] ?? 'institution';
        $scopeId = $parameters['scope_id'] ?? null;
        $metricType = $parameters['metric_type'] ?? self::getMetricTypeForName($metricName);
        $periodStart = $parameters['period_start'] ?? now()->startOfMonth();
        $periodEnd = $parameters['period_end'] ?? now()->endOfMonth();

        $value = self::calculateMetric($metricName, $parameters);

        return self::updateOrCreate([
            'metric_name' => $metricName,
            'scope' => $scope,
            'scope_id' => $scopeId,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ], [
            'metric_value' => $value,
            'metric_type' => $metricType,
            'metadata' => $parameters,
            'calculated_at' => now(),
        ]);
    }

    private static function getMetricTypeForName(string $metricName): string
    {
        return match ($metricName) {
            'total_syllabi', 'published_syllabi', 'draft_syllabi', 'pending_approval_syllabi', 'suggestions_count', 'pending_suggestions' => 'count',
            'syllabi_compliance_rate' => 'percentage',
            'average_approval_time' => 'duration',
            'quality_score_average' => 'score',
            default => 'count',
        };
    }
}
