<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Courses\Schemas;

use App\Constants\SyllabusConstants;
use App\Constants\CourseConstants;
use App\Models\Course;
use App\Models\Syllabus;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('code'),
                        TextEntry::make('college.name')
                            ->label('College'),
                        TextEntry::make('course_type')
                            ->label('Course Type')
                            ->badge()
                            ->color(fn ($state): string => is_string($state) ? CourseConstants::getTypeColor($state) : 'gray')
                            ->formatStateUsing(fn (string $state): string => CourseConstants::getTypeOptions()[$state] ?? ucfirst(str_replace('_', ' ', $state))),
                        TextEntry::make('description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Credit Units')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('credit_units_lecture')
                            ->label('Lecture Units')
                            ->suffix(' units')
                            ->placeholder('-'),
                        TextEntry::make('credit_units_laboratory')
                            ->label('Laboratory Units')
                            ->suffix(' units')
                            ->placeholder('-'),
                        TextEntry::make('total_credit_units')
                            ->label('Total Units')
                            ->suffix(' units')
                            ->placeholder('-'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Prerequisites')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('prerequisite_courses_display')
                            ->label('Prerequisite Courses')
                            ->state(function (Course $record): string {
                                $prerequisiteCourses = $record->prerequisiteCourses();
                                
                                if ($prerequisiteCourses->isEmpty()) {
                                    return 'None';
                                }
                                
                                return $prerequisiteCourses->pluck('name')->unique()->implode(', ');
                            })
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('Course Outcomes')
                    ->inlineLabel()
                    ->schema([
                        RepeatableEntry::make('outcomes')
                            ->label('')
                            ->schema([
                                TextEntry::make('verb')
                                    ->label('Action Verb')
                                    ->badge()
                                    ->color(fn ($state): string => is_string($state) ? SyllabusConstants::getActionVerbColor($state) : 'gray')
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                                TextEntry::make('content')
                                    ->label('Outcome Description')
                                    ->html()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->columnSpanFull()
                            ->visible(fn (Course $record): bool => !empty($record->outcomes)),
                        TextEntry::make('outcomes_placeholder')
                            ->label('Course Outcomes')
                            ->default('No outcomes defined')
                            ->visible(fn (Course $record): bool => empty($record->outcomes))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make()
                    ->inlineLabel()
                    ->schema([
                        IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean(),
                        TextEntry::make('sort_order')
                            ->label('Sort Order')
                            ->numeric(),
                    ]),
                Section::make()
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('deleted_at')
                            ->label('Deleted At')
                            ->dateTime()
                            ->visible(fn (Course $record): bool => $record->trashed())
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
