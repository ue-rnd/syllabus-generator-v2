<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Courses;

use App\Filament\Admin\Clusters\Academic\AcademicCluster;
use App\Filament\Admin\Clusters\Academic\Resources\Courses\Pages\CreateCourse;
use App\Filament\Admin\Clusters\Academic\Resources\Courses\Pages\EditCourse;
use App\Filament\Admin\Clusters\Academic\Resources\Courses\Pages\ListCourses;
use App\Filament\Admin\Clusters\Academic\Resources\Courses\Pages\ViewCourse;
use App\Filament\Admin\Clusters\Academic\Resources\Courses\RelationManagers\ProgramsRelationManager;
use App\Filament\Admin\Clusters\Academic\Resources\Courses\RelationManagers\SyllabiRelationManager;
use App\Filament\Admin\Clusters\Academic\Resources\Courses\Schemas\CourseForm;
use App\Filament\Admin\Clusters\Academic\Resources\Courses\Schemas\CourseInfolist;
use App\Filament\Admin\Clusters\Academic\Resources\Courses\Tables\CoursesTable;
use App\Models\Course;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $cluster = AcademicCluster::class;

    protected static ?int $navigationSort = 40;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::WrenchScrewdriver;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CourseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CourseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoursesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ProgramsRelationManager::class,
            SyllabiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'view' => ViewCourse::route('/{record}'),
            'edit' => EditCourse::route('/{record}/edit'),
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
            // Dean and Associate Dean can view all courses but only edit their college's
            return $query;
        }

        if ($user->position === 'department_chair') {
            // Department Chair can view all courses but only edit their college's
            return $query;
        }

        if ($user->position === 'faculty') {
            // Faculty can view all courses (read-only)
            return $query;
        }

        return $query->whereRaw('0 = 1');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('viewAny', Course::class);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create', Course::class);
    }
}
