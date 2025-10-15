<?php

namespace App\Filament\Admin\Clusters\QualityAssurance\Resources\QualityStandards\Schemas;

use App\Models\College;
use App\Models\Department;
use App\Models\QualityStandard;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\JsonEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QualityStandardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                Select::make('type')
                                    ->options(QualityStandard::getTypeOptions())
                                    ->required()
                                    ->native(false),

                                Select::make('category')
                                    ->options(QualityStandard::getCategoryOptions())
                                    ->required()
                                    ->native(false),
                            ]),

                        Textarea::make('description')
                            ->required()
                            ->rows(3),
                    ]),

                Section::make('Scope & Configuration')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('college_id')
                                    ->label('College')
                                    ->options(College::active()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (callable $set) => $set('department_id', null)),

                                Select::make('department_id')
                                    ->label('Department')
                                    ->options(fn (callable $get) => Department::where('college_id', $get('college_id'))->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (callable $get) => !$get('college_id')),

                                TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('minimum_score')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->suffix('%'),

                                TextInput::make('weight')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(1.00)
                                    ->minValue(0)
                                    ->maxValue(10),

                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('is_mandatory')
                                            ->label('Mandatory'),

                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true),
                                    ]),
                            ]),
                    ]),

                Section::make('Criteria Definition')
                    ->schema([
                        Repeater::make('criteria')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('criterion')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('weight')
                                            ->numeric()
                                            ->step(0.01)
                                            ->default(1.00)
                                            ->minValue(0)
                                            ->maxValue(10),
                                    ]),

                                Textarea::make('description')
                                    ->rows(2),

                                JsonEditor::make('validation_rules')
                                    ->label('Validation Rules (JSON)')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->addActionLabel('Add Criterion')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->cloneable(),
                    ])
                    ->collapsible(),
            ]);
    }
}