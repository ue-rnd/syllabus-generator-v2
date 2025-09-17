<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Course;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActiveCoursesWidget extends BaseWidget
{

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Course::query()
                    ->with(['college'])
                    ->active()
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Course Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Course Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('college.name')
                    ->label('College')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('units')
                    ->label('Units')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('course_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Core' => 'success',
                        'Elective' => 'warning',
                        'General Education' => 'info',
                        'Major' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('year_level')
                    ->label('Year Level')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->emptyStateHeading('No courses available')
            ->emptyStateDescription('Add courses to see them here.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }
}