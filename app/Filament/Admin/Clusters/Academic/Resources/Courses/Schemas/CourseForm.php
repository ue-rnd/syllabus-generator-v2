<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Courses\Schemas;

use App\Constants\CourseConstants;
use App\Constants\SyllabusConstants;
use App\Models\Course;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Course identification and college association')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('code')
                            ->required(),
                        Select::make('college_id')
                            ->label('College')
                            ->relationship('college', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                        RichEditor::make('description')
                            ->label('Description')
                            ->required()
                            ->toolbarButtons([['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table', 'attachFiles'],
                                ['undo', 'redo']])
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Credit Units')
                    ->description('Academic credit allocation')
                    ->schema([
                        TextInput::make('credit_units_lecture')
                            ->label('Lecture Credit Units')
                            ->numeric()
                            ->step(0.5)
                            ->default(3.0)
                            ->required()
                            ->suffix('units'),
                        TextInput::make('credit_units_laboratory')
                            ->label('Laboratory Credit Units')
                            ->numeric()
                            ->step(0.5)
                            ->default(0.0)
                            ->required()
                            ->suffix('units'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Course Details')
                    ->description('Course type and prerequisites')
                    ->schema([
                        Select::make('course_type')
                            ->label('Course Type')
                            ->options(CourseConstants::getTypeOptions())
                            ->searchable()
                            ->required(),
                        Select::make('prerequisite_courses')
                            ->label('Prerequisite Courses')
                            ->multiple()
                            ->options(fn () => Course::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Select prerequisite courses')
                            ->helperText('Select courses that must be completed before taking this course')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Section::make('Academic Content')
                    ->description('Course learning outcomes')
                    ->schema([
                        Repeater::make('outcomes')
                            ->label('Course Outcomes')
                            ->schema([
                                Select::make('verb')
                                    ->label('Action Verb')
                                    ->options(SyllabusConstants::ACTION_VERBS)
                                    ->searchable()
                                    ->required()
                                    ->placeholder('Select an action verb'),

                                RichEditor::make('content')
                                    ->label('Outcome Description')
                                    ->placeholder('Complete the outcome statement...')
                                    ->required()
                                    ->toolbarButtons([['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                        ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                        ['table', 'attachFiles'],
                                        ['undo', 'redo']])
                                    ->columnSpanFull(),
                            ])
                            ->addActionLabel('Add Course Outcome')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => isset($state['verb']) && isset($state['content'])
                                    ? ucfirst($state['verb']).' '.\Str::limit(strip_tags(is_string($state['content']) ? $state['content'] : ''), 50)
                                    : 'New Outcome'
                            )
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Configuration')
                    ->description('Display settings and status')
                    ->schema([
                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Is Active?')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
