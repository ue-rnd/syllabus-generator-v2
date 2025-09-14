<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Courses\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\Courses\CourseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCourse extends ViewRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
