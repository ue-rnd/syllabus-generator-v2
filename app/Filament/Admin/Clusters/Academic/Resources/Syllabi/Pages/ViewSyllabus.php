<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Actions\SyllabusApprovalActions;
use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\SyllabusResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSyllabus extends ViewRecord
{
    protected static string $resource = SyllabusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...SyllabusApprovalActions::getActionsForSyllabus($this->record),
            EditAction::make()
                ->visible(fn () => 
                    in_array($this->record->status, ['draft', 'for_revisions']) &&
                    ($this->record->principal_prepared_by === auth()->id() || 
                     collect($this->record->prepared_by)->contains('user_id', auth()->id()))
                ),
        ];
    }
}
