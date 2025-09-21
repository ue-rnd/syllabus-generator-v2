<?php

namespace App\Filament\Admin\Clusters\Academic;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class AcademicCluster extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $navigationLabel = 'Academic';

    protected static ?int $navigationSort = 10;
}
