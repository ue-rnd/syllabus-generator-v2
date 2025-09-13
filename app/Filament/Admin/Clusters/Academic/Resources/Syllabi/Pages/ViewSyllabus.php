<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\SyllabusResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSyllabus extends ViewRecord
{
    protected static string $resource = SyllabusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
