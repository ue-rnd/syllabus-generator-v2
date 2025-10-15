<?php

namespace App\Filament\Admin\Widgets;

use App\Models\StandardsCompliance;
use App\Models\Syllabus;
use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;

class ComplianceOverviewWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = true;

    public function getHeading(): string
    {
        return 'Standards Compliance Overview';
    }

    protected function getData(): array
    {
        $complianceData = StandardsCompliance::selectRaw('compliance_status, COUNT(*) as count')
            ->groupBy('compliance_status')
            ->pluck('count', 'compliance_status')
            ->toArray();

        $labels = [];
        $data = [];
        $colors = [];

        $statusMapping = [
            'compliant' => ['label' => 'Compliant', 'color' => Color::Green],
            'partially_compliant' => ['label' => 'Partially Compliant', 'color' => Color::Yellow],
            'non_compliant' => ['label' => 'Non-Compliant', 'color' => Color::Red],
            'not_assessed' => ['label' => 'Not Assessed', 'color' => Color::Gray],
        ];

        foreach ($statusMapping as $status => $config) {
            $labels[] = $config['label'];
            $data[] = $complianceData[$status] ?? 0;
            $colors[] = $config['color'][500]; // Use the 500 shade
        }

        return [
            'datasets' => [
                [
                    'label' => 'Compliance Status',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(34, 197, 94)', // Green
                        'rgb(234, 179, 8)', // Yellow
                        'rgb(239, 68, 68)', // Red
                        'rgb(107, 114, 128)', // Gray
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->can('viewAny', StandardsCompliance::class);
    }
}