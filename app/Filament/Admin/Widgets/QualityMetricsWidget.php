<?php

namespace App\Filament\Admin\Widgets;

use App\Services\QualityAssuranceService;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QualityMetricsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $qualityService = app(QualityAssuranceService::class);
        $metrics = $qualityService->getQualityMetrics([
            'period_start' => now()->subMonth(),
            'period_end' => now(),
        ]);

        return [
            Stat::make('Quality Checks Performed', $metrics['total_checks'])
                ->description('Last 30 days')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color(Color::Blue),

            Stat::make('Pass Rate', $metrics['pass_rate'] . '%')
                ->description($metrics['passed_checks'] . ' passed, ' . $metrics['failed_checks'] . ' failed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($this->getPassRateColor($metrics['pass_rate']))
                ->chart($this->getPassRateChart()),

            Stat::make('Average Quality Score', number_format($metrics['average_score'], 1))
                ->description('Out of 100 points')
                ->descriptionIcon('heroicon-m-star')
                ->color($this->getScoreColor($metrics['average_score'])),

            Stat::make('Need Improvement', $metrics['requires_improvement'])
                ->description('Syllabi requiring attention')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color(Color::Orange),
        ];
    }

    private function getPassRateColor(float $passRate): string
    {
        if ($passRate >= 90) return Color::Emerald;
        if ($passRate >= 75) return Color::Green;
        if ($passRate >= 60) return Color::Yellow;
        return Color::Red;
    }

    private function getScoreColor(float $score): string
    {
        if ($score >= 90) return Color::Emerald;
        if ($score >= 80) return Color::Green;
        if ($score >= 70) return Color::Yellow;
        if ($score >= 60) return Color::Orange;
        return Color::Red;
    }

    private function getPassRateChart(): array
    {
        // Generate a simple trend chart for the last 7 days
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            // Simulate pass rate data - in real implementation, this would query actual data
            $data[] = rand(70, 95);
        }

        return $data;
    }

    protected static bool $isLazy = true;

    public static function canView(): bool
    {
        return auth()->user()->can('viewAny', \App\Models\SyllabusQualityCheck::class);
    }
}