<?php

namespace App\Filament\Admin\Clusters\UserManagement;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class UserManagement extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $navigationLabel = 'User Management';

    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        return auth()->user()->can('view users') ||
               auth()->user()->can('assign roles') ||
               auth()->user()->can('manage permissions') ||
               auth()->user()->can('view system logs');
    }
}
