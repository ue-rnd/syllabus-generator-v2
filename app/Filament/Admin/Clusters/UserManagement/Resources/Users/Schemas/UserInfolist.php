<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Users\Schemas;

use App\Constants\UserConstants;
use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Full Name'),
                TextEntry::make('email')
                    ->label('Email Address'),
                TextEntry::make('position')
                    ->label('Position/Title')
                    ->badge()
                    ->color(fn ($state): string => is_string($state) ? UserConstants::getPositionColor($state) : 'gray')
                    ->formatStateUsing(fn ($state): string => is_string($state) ? (UserConstants::getPositionOptions()[$state] ?? ucfirst(str_replace('_', ' ', $state))) : 'Not specified')
                    ->placeholder('Not specified'),
                TextEntry::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'superadmin' => 'danger',
                        'admin' => 'warning',
                        'faculty' => 'success',
                        default => 'gray',
                    }),
                IconEntry::make('is_active')
                    ->label('Account Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextEntry::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime()
                    ->placeholder('Never logged in'),
                TextEntry::make('created_at')
                    ->label('Account Created')
                    ->dateTime(),
                TextEntry::make('deleted_at')
                    ->label('Account Deleted')
                    ->dateTime()
                    ->visible(fn (User $record): bool => $record->trashed()),
            ]);
    }
}
