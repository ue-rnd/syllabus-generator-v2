<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Schemas;

use App\Models\Faq;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('FAQ Information')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('question')
                            ->label('Question')
                            ->columnSpanFull(),
                        TextEntry::make('answer')
                            ->label('Answer')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Classification')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('category')
                            ->label('Category')
                            ->badge()
                            ->color(fn ($record) => $record->getCategoryColorAttribute())
                            ->formatStateUsing(fn ($state) => Faq::getCategoryOptions()[$state] ?? ucfirst(str_replace('_', ' ', $state))),
                        TextEntry::make('sort_order')
                            ->label('Sort Order')
                            ->numeric(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Tags')
                    ->inlineLabel()
                    ->schema([
                        RepeatableEntry::make('tags')
                            ->label('')
                            ->schema([
                                TextEntry::make('tag')
                                    ->badge()
                                    ->color('primary')
                                    ->state(fn ($state) => $state),
                            ])
                            ->columns(4)
                            ->columnSpanFull()
                            ->visible(fn ($record) => ! empty($record->tags)),

                        TextEntry::make('no_tags')
                            ->label('Tags')
                            ->default('No tags assigned')
                            ->visible(fn ($record) => empty($record->tags))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Publication Status')
                    ->inlineLabel()
                    ->schema([
                        IconEntry::make('is_published')
                            ->label('Published')
                            ->boolean()
                            ->trueIcon('heroicon-o-eye')
                            ->falseIcon('heroicon-o-eye-slash')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        IconEntry::make('is_featured')
                            ->label('Featured')
                            ->boolean()
                            ->trueIcon('heroicon-o-star')
                            ->falseIcon('heroicon-o-star')
                            ->trueColor('warning')
                            ->falseColor('gray'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Analytics')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('views_count')
                            ->label('Views')
                            ->numeric()
                            ->default(0),
                        TextEntry::make('helpful_count')
                            ->label('Helpful Votes')
                            ->numeric()
                            ->default(0)
                            ->color('success'),
                        TextEntry::make('not_helpful_count')
                            ->label('Not Helpful Votes')
                            ->numeric()
                            ->default(0)
                            ->color('danger'),
                        TextEntry::make('helpful_percentage')
                            ->label('Helpfulness')
                            ->state(fn ($record) => $record->getHelpfulPercentageAttribute() . '%')
                            ->color(fn ($record) => $record->getHelpfulPercentageAttribute() >= 70 ? 'success' : ($record->getHelpfulPercentageAttribute() >= 40 ? 'warning' : 'danger')),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),

                Section::make('Authorship')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('author.name')
                            ->label('Created By')
                            ->placeholder('-'),
                        TextEntry::make('lastUpdatedBy.name')
                            ->label('Last Updated By')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Archive Information')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('deleted_at')
                            ->label('Deleted At')
                            ->dateTime()
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->trashed())
                    ->columnSpanFull(),
            ]);
    }
}
