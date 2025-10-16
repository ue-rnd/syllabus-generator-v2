<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\BloomsTaxonomyVerbs;

use App\Filament\Admin\Clusters\Academic\AcademicCluster;
use App\Filament\Admin\Clusters\Academic\Resources\BloomsTaxonomyVerbs\Schemas\BloomsTaxonomyVerbForm;
use App\Filament\Admin\Clusters\Academic\Resources\BloomsTaxonomyVerbs\Tables\BloomsTaxonomyVerbsTable;
use App\Models\BloomsTaxonomyVerb;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BloomsTaxonomyVerbResource extends Resource
{
    protected static ?string $model = BloomsTaxonomyVerb::class;

    protected static ?string $cluster = AcademicCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $navigationLabel = 'Blooms Taxonomy Verbs';

    protected static ?string $modelLabel = 'Blooms Taxonomy Verb';

    protected static ?string $pluralModelLabel = 'Blooms Taxonomy Verbs';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return BloomsTaxonomyVerbForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BloomsTaxonomyVerbsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBloomsTaxonomyVerbs::route('/'),
            'create' => Pages\CreateBloomsTaxonomyVerb::route('/create'),
            'edit' => Pages\EditBloomsTaxonomyVerb::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        /** @var \Illuminate\Database\Eloquent\Builder|\App\Models\BloomsTaxonomyVerb $query */
        $query = parent::getEloquentQuery();

        return $query->ordered();
    }
}
