<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('firstname')
                    ->label('First Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('middlename')
                    ->label('Middle Name')
                    ->maxLength(255),
                TextInput::make('lastname')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('position')
                    ->label('Position/Title')
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(8)
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
                Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required()
                    ->multiple(false),
                Toggle::make('is_active')
                    ->label('Active Account')
                    ->default(true)
                    ->required(),
            ]);
    }
}
