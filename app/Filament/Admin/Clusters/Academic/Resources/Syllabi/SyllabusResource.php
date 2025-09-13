<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi;

use App\Filament\Admin\Clusters\Academic\AcademicCluster;
use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Pages\CreateSyllabus;
use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Pages\EditSyllabus;
use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Pages\ListSyllabi;
use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Pages\ViewSyllabus;
use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Schemas\SyllabusForm;
use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Schemas\SyllabusInfolist;
use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Tables\SyllabiTable;
use App\Models\Syllabus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SyllabusResource extends Resource
{
    protected static ?string $model = Syllabus::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PaperClip;

    protected static ?int $navigationSort = 50;

    protected static ?string $cluster = AcademicCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SyllabusForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SyllabusInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SyllabiTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSyllabi::route('/'),
            'create' => CreateSyllabus::route('/create'),
            'view' => ViewSyllabus::route('/{record}'),
            'edit' => EditSyllabus::route('/{record}/edit'),
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
