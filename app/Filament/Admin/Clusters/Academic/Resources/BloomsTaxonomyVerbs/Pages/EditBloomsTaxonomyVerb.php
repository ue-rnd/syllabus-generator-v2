<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\BloomsTaxonomyVerbs\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\BloomsTaxonomyVerbs\BloomsTaxonomyVerbResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBloomsTaxonomyVerb extends EditRecord
{
    protected static string $resource = BloomsTaxonomyVerbResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
