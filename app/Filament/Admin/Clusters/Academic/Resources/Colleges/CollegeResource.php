<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Colleges;

use App\Filament\Admin\Clusters\Academic\AcademicCluster;
use App\Filament\Admin\Clusters\Academic\Resources\Colleges\Pages\CreateCollege;
use App\Filament\Admin\Clusters\Academic\Resources\Colleges\Pages\EditCollege;
use App\Filament\Admin\Clusters\Academic\Resources\Colleges\Pages\ListColleges;
use App\Filament\Admin\Clusters\Academic\Resources\Colleges\Pages\ViewCollege;
use App\Filament\Admin\Clusters\Academic\Resources\Colleges\RelationManagers\DepartmentsRelationManager;
use App\Filament\Admin\Clusters\Academic\Resources\Colleges\Schemas\CollegeForm;
use App\Filament\Admin\Clusters\Academic\Resources\Colleges\Schemas\CollegeInfolist;
use App\Filament\Admin\Clusters\Academic\Resources\Colleges\Tables\CollegesTable;
use App\Models\College;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CollegeResource extends Resource
{
    protected static ?string $model = College::class;

    protected static ?string $cluster = AcademicCluster::class;

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CollegeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CollegeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CollegesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DepartmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListColleges::route('/'),
            'create' => CreateCollege::route('/create'),
            'view' => ViewCollege::route('/{record}'),
            'edit' => EditCollege::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
