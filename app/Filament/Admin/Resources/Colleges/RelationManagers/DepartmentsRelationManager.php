<?php

namespace App\Filament\Admin\Resources\Colleges\RelationManagers;

use App\Filament\Admin\Resources\Departments\DepartmentResource;
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
            ]);
    }
}
