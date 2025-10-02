<?php

namespace App\Filament\Admin\Clusters\SupportTraining;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class SupportTraining extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $navigationLabel = 'Support & Training';

    protected static ?int $navigationSort = 25;

    public static function canAccess(): bool
    {
        return auth()->user()->can('access admin panel');
    }
}