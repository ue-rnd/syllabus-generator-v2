<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Pages;

use App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\FaqResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFaq extends ViewRecord
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);

        // Increment view count when FAQ is viewed
        $this->record->incrementViews(auth()->user());
    }
}