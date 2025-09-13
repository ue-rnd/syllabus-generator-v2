<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Programs\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\Programs\ProgramResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProgram extends ViewRecord
{
    protected static string $resource = ProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
