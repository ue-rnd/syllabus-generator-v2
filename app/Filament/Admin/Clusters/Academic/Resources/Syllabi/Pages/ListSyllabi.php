<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\SyllabusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSyllabi extends ListRecords
{
    protected static string $resource = SyllabusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
