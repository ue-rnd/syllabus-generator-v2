<?php

namespace App\Filament\Admin\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static string $routePath = '/';

    protected static ?string $title = 'Dashboard';

    public function getTitle(): string|Htmlable
    {
        return 'Dashboard';
    }

    protected function getHeaderActions(): array
    {
        $user = auth()->user();

        $actions = [
            Action::make('create_syllabus')
                ->label('Create Syllabus')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(route('filament.admin.academic.resources.syllabi.create'))
                ->visible(fn () => $user->position === 'faculty' || $user->position === 'superadmin'),

            Action::make('view_syllabi')
                ->label('View All Syllabi')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->url(route('filament.admin.academic.resources.syllabi.index')),

            Action::make('view_courses')
                ->label('Manage Courses')
                ->icon('heroicon-o-book-open')
                ->color('info')
                ->url(route('filament.admin.academic.resources.courses.index'))
                ->visible(fn () => in_array($user->position, ['department_chair', 'associate_dean', 'dean', 'superadmin'])),

            Action::make('view_users')
                ->label('Manage Users')
                ->icon('heroicon-o-users')
                ->color('warning')
                ->url(route('filament.admin.user-management.resources.users.index'))
                ->visible(fn () => in_array($user->position, ['dean', 'superadmin'])),
        ];

        return array_filter($actions, fn ($action) => $action->isVisible());
    }

    public function getColumns(): int|array
    {
        return 2;
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\UserProfileWidget::class,
            \App\Filament\Admin\Widgets\PendingSyllabiWidget::class,
            \App\Filament\Admin\Widgets\StatsOverviewWidget::class,
            \App\Filament\Admin\Widgets\SyllabiGenerationChartWidget::class,
            \App\Filament\Admin\Widgets\CollegesChartWidget::class,
            \App\Filament\Admin\Widgets\LatestSyllabiWidget::class,
            \App\Filament\Admin\Widgets\ActiveCoursesWidget::class,
        ];
    }
}
