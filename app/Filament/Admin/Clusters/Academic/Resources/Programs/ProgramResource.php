<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Programs;

use App\Filament\Admin\Clusters\Academic\AcademicCluster;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Pages\CreateProgram;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Pages\EditProgram;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Pages\ListPrograms;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Pages\ViewProgram;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Schemas\ProgramForm;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Schemas\ProgramInfolist;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\Tables\ProgramsTable;
use App\Filament\Admin\Clusters\Academic\Resources\Programs\RelationManagers\CoursesRelationManager;
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
}
