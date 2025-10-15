<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials;

use App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Pages\CreateTutorial;
use App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Pages\EditTutorial;
use App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Pages\ListTutorials;
use App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Pages\ViewTutorial;
use App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Schemas\TutorialForm;
use App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Schemas\TutorialInfolist;
use App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Tables\TutorialsTable;
use App\Filament\Admin\Clusters\SupportTraining\SupportTrainingCluster;
use App\Models\Tutorial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TutorialResource extends Resource
{
    protected static ?string $model = Tutorial::class;

    protected static ?string $cluster = SupportTrainingCluster::class;

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PlayCircle;

    protected static ?string $navigationLabel = 'Tutorials';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return TutorialForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TutorialInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TutorialsTable::configure($table);
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
            'index' => ListTutorials::route('/'),
            'create' => CreateTutorial::route('/create'),
            'view' => ViewTutorial::route('/{record}'),
            'edit' => EditTutorial::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canViewAny(): bool
    {
        return true; // All users can view tutorials
    }

    public static function canView($record): bool
    {
        return true; // All users can view individual tutorials
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create custom reports') || // Reusing permissions
               auth()->user()->can('manage system settings');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('manage system settings') ||
               ($record->author_id === auth()->id());
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('manage system settings') ||
               ($record->author_id === auth()->id());
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('manage system settings');
    }

    public static function getNavigationBadge(): ?string
    {
        $unpublishedCount = static::getModel()::where('is_published', false)->count();

        return $unpublishedCount > 0 ? (string) $unpublishedCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() ? 'warning' : null;
    }
}
