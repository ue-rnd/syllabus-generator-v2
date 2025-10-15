<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Pages;

use App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\TutorialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTutorial extends EditRecord
{
    protected static string $resource = TutorialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
