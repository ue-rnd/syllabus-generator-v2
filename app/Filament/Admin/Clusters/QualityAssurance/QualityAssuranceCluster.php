<?php

namespace App\Filament\Admin\Clusters\QualityAssurance;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class QualityAssuranceCluster extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;

    protected static ?string $navigationLabel = 'Quality Assurance';

    protected static ?int $navigationSort = 20;

    protected static ?string $slug = 'quality-assurance';
}
