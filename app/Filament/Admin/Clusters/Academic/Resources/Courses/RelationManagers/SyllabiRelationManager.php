<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Courses\RelationManagers;

use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\SyllabusResource;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class SyllabiRelationManager extends RelationManager
{
    protected static string $relationship = 'syllabi';

    protected static ?string $relatedResource = SyllabusResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name']),
            ]);
    }
}
