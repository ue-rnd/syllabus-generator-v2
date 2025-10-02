<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Tables;

use App\Models\Tutorial;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TutorialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->description)
                    ->limit(50),

                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color(fn ($record) => $record->getCategoryColorAttribute())
                    ->formatStateUsing(fn ($state) => Tutorial::getCategoryOptions()[$state] ?? ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),

                TextColumn::make('difficulty_level')
                    ->label('Difficulty')
                    ->badge()
                    ->color(fn ($record) => $record->getDifficultyColorAttribute())
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->formatStateUsing(fn ($state) => $state ? "{$state} min" : 'N/A')
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                IconColumn::make('featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),

                TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('helpful_percentage')
                    ->label('Helpful %')
                    ->formatStateUsing(fn ($record) => $record->getHelpfulPercentageAttribute() . '%')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->selectRaw('*, (helpful_count / NULLIF(helpful_count + not_helpful_count, 0)) * 100 as helpful_percentage')
                            ->orderBy('helpful_percentage', $direction);
                    })
                    ->toggleable(),

                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(Tutorial::getCategoryOptions())
                    ->searchable(),

                SelectFilter::make('difficulty_level')
                    ->label('Difficulty')
                    ->options(Tutorial::getDifficultyOptions()),

                TernaryFilter::make('is_published')
                    ->label('Published')
                    ->placeholder('All tutorials')
                    ->trueLabel('Published only')
                    ->falseLabel('Unpublished only'),

                TernaryFilter::make('featured')
                    ->label('Featured')
                    ->placeholder('All tutorials')
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured'),

                SelectFilter::make('author_id')
                    ->label('Author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn ($record) => route('filament.admin.resources.tutorials.view', $record));
    }
}