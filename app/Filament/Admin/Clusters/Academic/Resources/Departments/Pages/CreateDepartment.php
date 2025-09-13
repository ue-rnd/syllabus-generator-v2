<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Departments\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\Departments\DepartmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;
}
