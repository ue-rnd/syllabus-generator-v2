<?php

namespace App\Filament\Admin\Widgets;

use App\Models\College;
use Filament\Widgets\ChartWidget;

class CollegesChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = ['sm' => 'full', 'lg' => 5];

    protected function getData(): array
    {
        $colleges = College::withCount(['departments', 'programs', 'courses'])
            ->active()
            ->ordered()
            ->get();

        $labels = [];
        $departmentData = [];
        $programData = [];
        $courseData = [];

        foreach ($colleges as $college) {
            $labels[] = $college->code ?: substr($college->name, 0, 10);
            $departmentData[] = $college->departments_count;
            $programData[] = $college->programs_count;
            $courseData[] = $college->courses_count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Departments',
                    'data' => $departmentData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Programs',
                    'data' => $programData,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                    'borderColor' => 'rgb(245, 158, 11)',
                ],
                [
                    'label' => 'Courses',
                    'data' => $courseData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
