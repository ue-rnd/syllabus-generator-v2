<?php

namespace App\Filament\Admin\Clusters\QualityAssurance\Resources\QualityStandards\Tables;

use App\Models\QualityStandard;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class QualityStandardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'institutional' => 'blue',
                        'accreditation' => 'green',
                        'departmental' => 'yellow',
                        'program' => 'purple',
                        'course' => 'orange',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'content' => 'blue',
                        'structure' => 'green',
                        'assessment' => 'orange',
                        'learning_outcomes' => 'purple',
                        'resources' => 'pink',
                        'policies' => 'indigo',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('college.name')
                    ->label('College')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('minimum_score')
                    ->label('Min Score')
                    ->suffix('%')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('weight')
                    ->alignEnd()
                    ->sortable(),

                IconColumn::make('is_mandatory')
                    ->label('Mandatory')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor(Color::Green)
                    ->falseColor(Color::Gray),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor(Color::Green)
                    ->falseColor(Color::Red),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(QualityStandard::getTypeOptions()),

                SelectFilter::make('category')
                    ->options(QualityStandard::getCategoryOptions()),

                TernaryFilter::make('is_mandatory')
                    ->label('Mandatory Standards')
                    ->placeholder('All standards')
                    ->trueLabel('Mandatory only')
                    ->falseLabel('Optional only'),

                TernaryFilter::make('is_active')
                    ->label('Active Standards')
                    ->placeholder('All standards')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }
}
