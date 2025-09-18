<?php

namespace App\Filament\Admin\Clusters\UserManagement;

use Filament\Clusters\Cluster;
use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class UserManagement extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $navigationLabel = 'User Management';

    protected static ?int $navigationSort = 20;
}
