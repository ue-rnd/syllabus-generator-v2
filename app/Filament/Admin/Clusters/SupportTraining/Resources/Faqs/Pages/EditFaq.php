<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Pages;

use App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\FaqResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaq extends EditRecord
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['last_updated_by'] = auth()->id();
        return $data;
    }
}