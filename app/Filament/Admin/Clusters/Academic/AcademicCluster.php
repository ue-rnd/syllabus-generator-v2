<?php

namespace App\Filament\Admin\Clusters\Academic;

use Filament\Clusters\Cluster;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Pages\Enums\SubNavigationPosition;

class AcademicCluster extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $navigationLabel = 'Academic';

    protected static ?int $navigationSort = 10;
}
