<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Courses\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\Courses\CourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;
}
