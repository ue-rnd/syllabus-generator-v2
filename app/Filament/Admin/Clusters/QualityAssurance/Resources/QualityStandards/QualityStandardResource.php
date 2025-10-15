<?php

namespace App\Filament\Admin\Clusters\QualityAssurance\Resources\QualityStandards;

use App\Filament\Admin\Clusters\QualityAssurance\QualityAssuranceCluster;
use App\Filament\Admin\Clusters\QualityAssurance\Resources\QualityStandards\Pages\CreateQualityStandard;
use App\Filament\Admin\Clusters\QualityAssurance\Resources\QualityStandards\Pages\EditQualityStandard;
use App\Filament\Admin\Clusters\QualityAssurance\Resources\QualityStandards\Pages\ListQualityStandards;
use App\Filament\Admin\Clusters\QualityAssurance\Resources\QualityStandards\Pages\ViewQualityStandard;
use App\Filament\Admin\Clusters\QualityAssurance\Resources\QualityStandards\Schemas\QualityStandardForm;
use App\Filament\Admin\Clusters\QualityAssurance\Resources\QualityStandards\Schemas\QualityStandardInfolist;
use App\Filament\Admin\Clusters\QualityAssurance\Resources\QualityStandards\Tables\QualityStandardsTable;
use App\Models\QualityStandard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QualityStandardResource extends Resource
{
    protected static ?string $model = QualityStandard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $navigationLabel = 'Quality Standards';

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = QualityAssuranceCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return QualityStandardForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QualityStandardInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QualityStandardsTable::configure($table);
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
            // 'index' => ListQualityStandards::route('/'),
            // 'create' => CreateQualityStandard::route('/create'),
            // 'view' => ViewQualityStandard::route('/{record}'),
            // 'edit' => EditQualityStandard::route('/{record}/edit'),
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
        return auth()->user()->can('viewAny', QualityStandard::class);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create', QualityStandard::class);
    }
}