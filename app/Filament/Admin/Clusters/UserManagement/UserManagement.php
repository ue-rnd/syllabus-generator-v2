<?php

namespace App\Filament\Admin\Clusters\UserManagement;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class UserManagement extends Cluster
{

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $navigationLabel = 'User Management';

    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        
        // Superadmins always have access
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        return $user->can('view users') ||
               $user->can('assign roles') ||
               $user->can('manage permissions') ||
               $user->can('view system logs');
    }
}
