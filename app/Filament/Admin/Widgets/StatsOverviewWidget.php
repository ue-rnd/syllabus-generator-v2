<?php

namespace App\Filament\Admin\Widgets;

use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\Program;
use App\Models\Syllabus;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Colleges', College::count())
                ->description('Active educational institutions')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Total Departments', Department::count())
                ->description('Academic departments')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info')
                ->chart([15, 4, 10, 2, 12, 4, 12]),

            Stat::make('Total Programs', Program::count())
                ->description('Available degree programs')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Total Courses', Course::count())
                ->description('Course offerings')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary')
                ->chart([17, 16, 14, 15, 14, 13, 12]),

            Stat::make('Total Syllabi', Syllabus::count())
                ->description('Generated syllabi')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success')
                ->chart([3, 2, 5, 3, 4, 2, 6]),

            Stat::make('System Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray')
                ->chart([1, 2, 1, 3, 2, 4, 3]),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
