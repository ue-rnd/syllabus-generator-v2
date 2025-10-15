<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Actions\SyllabusApprovalActions;
use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\SyllabusResource;
use App\Models\Syllabus;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSyllabus extends ViewRecord
{
    protected static string $resource = SyllabusResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Syllabus $record */
        $record = $this->record;
        
        return [
            ...SyllabusApprovalActions::getActionsForSyllabus($record),
            EditAction::make()
                ->visible(
                    fn () => in_array($record->status, ['draft', 'for_revisions']) &&
                    ($record->principal_prepared_by === auth()->id() ||
                     collect($record->prepared_by)->contains('user_id', auth()->id()))
                ),
        ];
    }
}
