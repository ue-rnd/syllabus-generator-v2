<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\SyllabusSuggestions\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\SyllabusSuggestions\SyllabusSuggestionsResource;
use Filament\Resources\Pages\ListRecords;

class ListSyllabusSuggestions extends ListRecords
{
    protected static string $resource = SyllabusSuggestionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action since suggestions are created through syllabus interface
        ];
    }
}
