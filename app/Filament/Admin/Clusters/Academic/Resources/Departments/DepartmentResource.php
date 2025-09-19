<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Departments;

use App\Filament\Admin\Clusters\Academic\AcademicCluster;
use App\Filament\Admin\Clusters\Academic\Resources\Departments\Pages\CreateDepartment;
use App\Filament\Admin\Clusters\Academic\Resources\Departments\Pages\EditDepartment;
use App\Filament\Admin\Clusters\Academic\Resources\Departments\Pages\ListDepartments;
use App\Filament\Admin\Clusters\Academic\Resources\Departments\Pages\ViewDepartment;
use App\Filament\Admin\Clusters\Academic\Resources\Departments\Schemas\DepartmentForm;
use App\Filament\Admin\Clusters\Academic\Resources\Departments\Schemas\DepartmentInfolist;
use App\Filament\Admin\Clusters\Academic\Resources\Departments\Tables\DepartmentsTable;
use App\Models\Department;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $cluster = AcademicCluster::class;

    protected static ?int $navigationSort = 20;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingLibrary;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DepartmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DepartmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepartmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'view' => ViewDepartment::route('/{record}'),
            'edit' => EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        if ($user->position === 'superadmin') {
            return $query;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            // Dean and Associate Dean can view all departments but only edit their college's
            return $query;
        }

        if ($user->position === 'department_chair') {
            // Department Chair can view all departments but only edit their own
            return $query;
        }

        if ($user->position === 'faculty') {
            // Faculty can view all departments (read-only)
            return $query;
        }

        return $query->whereRaw('0 = 1');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('viewAny', Department::class);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create', Department::class);
    }
}
