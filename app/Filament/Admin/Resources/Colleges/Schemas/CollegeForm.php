<?php

namespace App\Filament\Admin\Resources\Colleges\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CollegeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('mission')
                    ->columnSpanFull(),
                Textarea::make('vision')
                    ->columnSpanFull(),
                Textarea::make('core_values')
                    ->columnSpanFull(),
                Textarea::make('objectives')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('logo_path'),
            ]);
    }
}
