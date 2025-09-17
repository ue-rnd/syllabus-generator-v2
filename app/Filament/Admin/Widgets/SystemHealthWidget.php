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

class SystemHealthWidget extends BaseWidget
{

    protected static ?int $sort = 6;

    protected function getStats(): array
    {
        $activeColleges = College::active()->count();
        $totalColleges = College::count();
        $activeDepartments = Department::active()->count();
        $totalDepartments = Department::count();
        $activePrograms = Program::active()->count();
        $totalPrograms = Program::count();

        return [
            Stat::make('Active Colleges', $activeColleges . ' / ' . $totalColleges)
                ->description('Operational colleges')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color($activeColleges === $totalColleges ? 'success' : 'warning'),

            Stat::make('Active Departments', $activeDepartments . ' / ' . $totalDepartments)
                ->description('Operational departments')
                ->descriptionIcon('heroicon-m-building-office')
                ->color($activeDepartments === $totalDepartments ? 'success' : 'warning'),

            Stat::make('Active Programs', $activePrograms . ' / ' . $totalPrograms)
                ->description('Available programs')
                ->descriptionIcon('heroicon-m-book-open')
                ->color($activePrograms === $totalPrograms ? 'success' : 'warning'),

            Stat::make('Courses per College', number_format(Course::count() / max(College::count(), 1), 1))
                ->description('Average distribution')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),

            Stat::make('Recent Activity', Syllabus::whereDate('created_at', today())->count())
                ->description('Syllabi created today')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),

            Stat::make('System Users', User::count())
                ->description('Total registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}