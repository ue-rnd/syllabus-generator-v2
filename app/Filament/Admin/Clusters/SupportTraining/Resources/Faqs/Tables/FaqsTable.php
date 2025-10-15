<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Tables;

use App\Models\Faq;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FaqsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')
                    ->label('Question')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(60),

                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color(fn ($record) => $record->getCategoryColorAttribute())
                    ->formatStateUsing(fn ($state) => Faq::getCategoryOptions()[$state] ?? ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                IconColumn::make('is_featured')
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
                    ->sortable()
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
                    ->options(Faq::getCategoryOptions())
                    ->searchable(),

                TernaryFilter::make('is_published')
                    ->label('Published')
                    ->placeholder('All FAQs')
                    ->trueLabel('Published only')
                    ->falseLabel('Unpublished only'),

                TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('All FAQs')
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured'),

                SelectFilter::make('author_id')
                    ->label('Author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->recordUrl(fn ($record) => route('filament.admin.resources.faqs.view', $record));
    }
}
