<?php

namespace App\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class Academic extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $navigationLabel = 'Academic';

    protected static ?int $navigationSort = 10;
}
