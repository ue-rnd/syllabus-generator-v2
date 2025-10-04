<?php

namespace App\Filament\Admin\Clusters\SupportTraining;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class SupportTrainingCluster extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::QuestionMarkCircle;

    protected static ?string $navigationLabel = 'Support & Training';

    protected static ?int $navigationSort = 50;

    protected static ?string $slug = 'support-training';

    // public static function canAccess(): bool
    // {
    //     return auth()->user()->can('access admin panel');
    // }
}