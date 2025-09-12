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
                TextInput::make('code')
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
                Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->required()
                    ->searchable(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('outcomes')
                    ->columnSpanFull(),
                Textarea::make('objectives')
                    ->columnSpanFull(),
                Select::make('courses')
                    ->label('Courses')
                    ->relationship('courses', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
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
            ]);
    }
}
