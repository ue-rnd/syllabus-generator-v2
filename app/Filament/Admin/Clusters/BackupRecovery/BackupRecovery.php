<?php

namespace App\Filament\Admin\Clusters\BackupRecovery;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class BackupRecovery extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CircleStack;

    protected static ?string $navigationLabel = 'Backup & Recovery';

    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        return auth()->user()->can('manage system settings') ||
               auth()->user()->can('manage backups');
    }
}