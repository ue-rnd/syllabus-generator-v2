<?php

namespace App\Filament\Admin\Resources\Roles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MultiSelect;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                MultiSelect::make('permissions')
                    ->relationship('permissions', 'name')
                    ->preload()
                    ->multiple()
                    ->searchable(),
            ]);
    }
}
