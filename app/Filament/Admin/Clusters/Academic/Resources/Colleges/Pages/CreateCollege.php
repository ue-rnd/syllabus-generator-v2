<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Colleges\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\Colleges\CollegeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCollege extends CreateRecord
{
    protected static string $resource = CollegeResource::class;
}
