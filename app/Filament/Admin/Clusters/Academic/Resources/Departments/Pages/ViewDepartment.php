<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Departments\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\Departments\DepartmentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDepartment extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
