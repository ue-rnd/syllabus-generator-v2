<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Pages;

use App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\FaqResource;
use App\Models\Faq;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFaqs extends ListRecords
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All FAQs')
                ->badge(Faq::count()),

            'published' => Tab::make('Published')
                ->modifyQueryUsing(function (Builder $query) {
                    /** @var \Illuminate\Database\Eloquent\Builder<\App\Models\Faq> $query */
                    return $query->published();
                })
                ->badge(Faq::query()->published()->count()),

            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', false))
                ->badge(Faq::where('is_published', false)->count())
                ->badgeColor('warning'),

            'featured' => Tab::make('Featured')
                ->modifyQueryUsing(function (Builder $query) {
                    /** @var \Illuminate\Database\Eloquent\Builder<\App\Models\Faq> $query */
                    return $query->featured();
                })
                ->badge(Faq::query()->featured()->count())
                ->badgeColor('success'),

            'popular' => Tab::make('Popular')
                ->modifyQueryUsing(function (Builder $query) {
                    /** @var \Illuminate\Database\Eloquent\Builder<\App\Models\Faq> $query */
                    return $query->popular();
                })
                ->badge(Faq::where('views_count', '>', 50)->count()),
        ];
    }
}
