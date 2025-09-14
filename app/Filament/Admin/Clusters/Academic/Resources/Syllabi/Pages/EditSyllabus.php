<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\SyllabusResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSyllabus extends EditRecord
{
    protected static string $resource = SyllabusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
