<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Programs;

use App\Filament\Admin\Clusters\Academic\AcademicCluster;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Pages\CreateProgram;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Pages\EditProgram;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Pages\ListPrograms;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Pages\ViewProgram;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\RelationManagers\CoursesRelationManager;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Schemas\ProgramForm;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Schemas\ProgramInfolist;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Tables\ProgramsTable;
use App\Models\Program;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $cluster = AcademicCluster::class;

    protected static ?int $navigationSort = 30;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProgramForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProgramInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgramsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrograms::route('/'),
            'create' => CreateProgram::route('/create'),
            'view' => ViewProgram::route('/{record}'),
            'edit' => EditProgram::route('/{record}/edit'),
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
            // Dean and Associate Dean can view all programs but only edit their college's
            return $query;
        }

        if ($user->position === 'department_chair') {
            // Department Chair can view all programs but only edit their department's
            return $query;
        }

        if ($user->position === 'faculty') {
            // Faculty can view all programs (read-only)
            return $query;
        }

        return $query->whereRaw('0 = 1');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('viewAny', Program::class);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create', Program::class);
    }
}
