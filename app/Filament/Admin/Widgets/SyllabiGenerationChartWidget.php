<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Syllabus;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SyllabiGenerationChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Syllabus::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $dates = [];
        $counts = [];

        // Fill in missing dates with 0
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = Carbon::parse($date)->format('M j');
            $counts[] = $data[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Syllabi Generated',
                    'data' => $counts,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.1,
                ],
            ],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                        'maxTicksLimit' => 6,
                    ],
                ],

            ],
        ];
    }
}
