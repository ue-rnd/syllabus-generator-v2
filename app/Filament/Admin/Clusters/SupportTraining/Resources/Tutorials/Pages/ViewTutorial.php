<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Pages;

use App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\TutorialResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

/**
 * @property \App\Models\Tutorial $record
 */
class ViewTutorial extends ViewRecord
{
    protected static string $resource = TutorialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Increment view count when tutorial is viewed
        $this->record->incrementViews(auth()->user());
    }
}
