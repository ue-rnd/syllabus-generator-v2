<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Syllabus;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestSyllabiWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Syllabus::query()
                    ->with(['course'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('course.code')
                    ->label('Course Code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Academic Year')
                    ->formatStateUsing(function ($record) {
                        return $record->ay_start . '-' . $record->ay_end;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->recordActions([
                Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Syllabus $record): string => route('syllabus.pdf.view', $record)),

                Action::make('download')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->url(fn (Syllabus $record): string => route('syllabus.pdf.download', $record)),
            ])
            ->emptyStateHeading('No syllabi created yet')
            ->emptyStateDescription('Create your first syllabus to see it here.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
