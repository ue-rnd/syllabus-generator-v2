<?php

namespace App\Filament\Admin\Resources\Programs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Course;
use App\Models\Department;

class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('level')
                    ->required()
                    ->options([
                        'ASSOCIATE' => 'Associate',
                        'BACHELOR' => 'Bachelor',
                        'MASTERAL' => 'Masteral',
                        'DOCTORAL' => 'Doctoral',
                    ])
                    ->default('ASSOCIATE')
                    ->searchable(),
                TextInput::make('code')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('outcomes')
                    ->columnSpanFull(),
                Textarea::make('objectives')
                    ->columnSpanFull(),
                Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->required()
                    ->searchable(),
                Select::make('courses')
                    ->label('Courses')
                    ->relationship('courses', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
