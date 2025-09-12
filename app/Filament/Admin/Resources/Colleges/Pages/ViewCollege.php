<?php

namespace App\Filament\Admin\Resources\Colleges\Pages;

use App\Filament\Admin\Resources\Colleges\CollegeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCollege extends ViewRecord
{
    protected static string $resource = CollegeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
