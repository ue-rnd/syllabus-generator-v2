<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Schemas;

use App\Models\Tutorial;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TutorialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tutorial Information')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title')
                            ->columnSpanFull(),
                        TextEntry::make('slug')
                            ->label('Slug')
                            ->color('gray')
                            ->copyable(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('No description provided')
                            ->columnSpanFull(),
                        TextEntry::make('content')
                            ->label('Content')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Classification & Settings')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('category')
                            ->label('Category')
                            ->badge()
                            ->color(fn ($record) => $record->getCategoryColorAttribute())
                            ->formatStateUsing(fn ($state) => Tutorial::getCategoryOptions()[$state] ?? ucfirst(str_replace('_', ' ', $state))),
                        TextEntry::make('difficulty_level')
                            ->label('Difficulty Level')
                            ->badge()
                            ->color(fn ($record) => $record->getDifficultyColorAttribute())
                            ->formatStateUsing(fn ($state) => Tutorial::getDifficultyOptions()[$state] ?? ucfirst($state)),
                        TextEntry::make('duration_minutes')
                            ->label('Duration')
                            ->suffix(' minutes')
                            ->placeholder('-'),
                        TextEntry::make('estimated_read_time')
                            ->label('Estimated Read Time')
                            ->state(fn ($record) => $record->getEstimatedReadTimeAttribute()),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),

                Section::make('Media & Resources')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('video_url')
                            ->label('Video URL')
                            ->placeholder('No video attached')
                            ->url(fn ($record) => $record->video_url)
                            ->openUrlInNewTab()
                            ->columnSpanFull(),
                        RepeatableEntry::make('attachments')
                            ->label('Attachments')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('File Name'),
                                TextEntry::make('url')
                                    ->label('Download')
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab(),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->attachments)),
                        TextEntry::make('no_attachments')
                            ->label('Attachments')
                            ->default('No attachments')
                            ->visible(fn ($record) => empty($record->attachments))
                            ->columnSpanFull(),
                    ])
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
                            ->visible(fn ($record) => !empty($record->tags)),
                        
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
                        IconEntry::make('featured')
                            ->label('Featured')
                            ->boolean()
                            ->trueIcon('heroicon-o-star')
                            ->falseIcon('heroicon-o-star')
                            ->trueColor('warning')
                            ->falseColor('gray'),
                        TextEntry::make('sort_order')
                            ->label('Sort Order')
                            ->numeric(),
                    ])
                    ->columns(3)
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
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columns(3)
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