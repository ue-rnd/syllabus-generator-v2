<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Courses\RelationManagers;

use App\Filament\Admin\Clusters\Academic\Resources\Programs\ProgramResource;
use Filament\Actions\CreateAction;
use Filament\Actions\AttachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ProgramsRelationManager extends RelationManager
{
    protected static string $relationship = 'programs';

    protected static ?string $relatedResource = ProgramResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'code']),
            ]);
    }
}
