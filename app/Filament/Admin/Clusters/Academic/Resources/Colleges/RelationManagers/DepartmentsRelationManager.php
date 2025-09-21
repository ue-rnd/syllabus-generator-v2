<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Colleges\RelationManagers;

use App\Filament\Admin\Clusters\Academic\Resources\Departments\DepartmentResource;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class DepartmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'departments';

    protected static ?string $relatedResource = DepartmentResource::class;

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
