<?php

namespace App\Filament\Admin\Widgets;

use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\Program;
use App\Models\Syllabus;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuickStatsWidget extends BaseWidget
{

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $thisWeekSyllabi = Syllabus::where('created_at', '>=', Carbon::now()->startOfWeek())->count();
        $lastWeekSyllabi = Syllabus::whereBetween('created_at', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ])->count();

        $syllabusGrowth = $lastWeekSyllabi > 0
            ? round((($thisWeekSyllabi - $lastWeekSyllabi) / $lastWeekSyllabi) * 100, 1)
            : 0;

        return [
            Stat::make('This Week\'s Syllabi', $thisWeekSyllabi)
                ->description($syllabusGrowth >= 0 ? "+{$syllabusGrowth}% from last week" : "{$syllabusGrowth}% from last week")
                ->descriptionIcon($syllabusGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($syllabusGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('Active Institutions', College::active()->count())
                ->description('Colleges currently active')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Stat::make('Course Catalog', Course::active()->count())
                ->description('Available course offerings')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('warning'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}