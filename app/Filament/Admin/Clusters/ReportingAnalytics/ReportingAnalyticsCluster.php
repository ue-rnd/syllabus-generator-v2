<?php

namespace App\Filament\Admin\Clusters\ReportingAnalytics;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class ReportingAnalyticsCluster extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;

    protected static ?string $navigationLabel = 'Reports & Analytics';

    protected static ?int $navigationSort = 30;

    protected static ?string $slug = 'reporting-analytics';
}