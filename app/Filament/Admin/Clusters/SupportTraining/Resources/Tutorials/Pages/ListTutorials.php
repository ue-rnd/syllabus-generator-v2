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
                ->modifyQueryUsing(function (Builder $query) {
                    /** @var \Illuminate\Database\Eloquent\Builder<\App\Models\Tutorial> $query */
                    return $query->published();
                })
                ->badge(Tutorial::query()->published()->count()),

            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', false))
                ->badge(Tutorial::where('is_published', false)->count())
                ->badgeColor('warning'),

            'featured' => Tab::make('Featured')
                ->modifyQueryUsing(function (Builder $query) {
                    /** @var \Illuminate\Database\Eloquent\Builder<\App\Models\Tutorial> $query */
                    return $query->featured();
                })
                ->badge(Tutorial::query()->featured()->count())
                ->badgeColor('success'),

            'popular' => Tab::make('Popular')
                ->modifyQueryUsing(function (Builder $query) {
                    /** @var \Illuminate\Database\Eloquent\Builder<\App\Models\Tutorial> $query */
                    return $query->popular();
                })
                ->badge(Tutorial::where('views_count', '>', 100)->count()),
        ];
    }
}
