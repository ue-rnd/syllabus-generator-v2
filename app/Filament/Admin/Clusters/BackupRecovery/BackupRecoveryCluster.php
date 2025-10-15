<?php

namespace App\Filament\Admin\Clusters\BackupRecovery;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class BackupRecoveryCluster extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CircleStack;

    protected static ?string $navigationLabel = 'Backup & Recovery';

    protected static ?int $navigationSort = 40;

    protected static ?string $slug = 'backup-recovery';

    public static function canAccess(): bool
    {
        return auth()->user()->can('manage system settings') ||
               auth()->user()->can('manage backups');
    }
}
