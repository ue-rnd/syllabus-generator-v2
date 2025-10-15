<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs;

use App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Pages\EditFaq;
use App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Pages\ListFaqs;
use App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Pages\ViewFaq;
use App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Schemas\FaqForm;
use App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Schemas\FaqInfolist;
use App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Tables\FaqsTable;
use App\Filament\Admin\Clusters\SupportTraining\SupportTrainingCluster;
use App\Models\Faq;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $cluster = SupportTrainingCluster::class;

    protected static ?int $navigationSort = 20;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::QuestionMarkCircle;

    protected static ?string $navigationLabel = 'FAQs';

    protected static ?string $recordTitleAttribute = 'question';

    public static function form(Schema $schema): Schema
    {
        return FaqForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FaqInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FaqsTable::configure($table);
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
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'view' => ViewFaq::route('/{record}'),
            'edit' => EditFaq::route('/{record}/edit'),
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
        return true; // All users can view FAQs
    }

    public static function canView($record): bool
    {
        return true; // All users can view individual FAQs
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
