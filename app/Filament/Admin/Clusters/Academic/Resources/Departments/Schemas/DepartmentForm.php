<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Departments\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Department identification and college association')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        Select::make('college_id')
                            ->relationship(name: 'college', titleAttribute: 'name')
                            ->preload()
                            ->searchable(),
                        Select::make('department_chair_id')
                            ->label('Department Chair')
                            ->relationship('departmentChair', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->searchable(['firstname', 'lastname', 'middlename', 'name'])
                            ->preload()
                            ->nullable()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
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
