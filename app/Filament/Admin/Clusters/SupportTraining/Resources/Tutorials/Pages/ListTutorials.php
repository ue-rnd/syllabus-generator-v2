<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Pages;

use App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\TutorialResource;
use App\Models\Tutorial;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTutorials extends ListRecords
{
    protected static string $resource = TutorialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Tutorials')
                ->badge(Tutorial::count()),

            'published' => Tab::make('Published')
                ->modifyQueryUsing(fn (Builder $query) => $query->published())
                ->badge(Tutorial::published()->count()),

            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', false))
                ->badge(Tutorial::where('is_published', false)->count())
                ->badgeColor('warning'),

            'featured' => Tab::make('Featured')
                ->modifyQueryUsing(fn (Builder $query) => $query->featured())
                ->badge(Tutorial::featured()->count())
                ->badgeColor('success'),

            'popular' => Tab::make('Popular')
                ->modifyQueryUsing(fn (Builder $query) => $query->popular())
                ->badge(Tutorial::where('views_count', '>', 100)->count()),
        ];
    }
}
