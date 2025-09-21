<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Roles;

use App\Filament\Admin\Clusters\UserManagement\Resources\Roles\Pages\CreateRole;
use App\Filament\Admin\Clusters\UserManagement\Resources\Roles\Pages\EditRole;
use App\Filament\Admin\Clusters\UserManagement\Resources\Roles\Pages\ListRoles;
use App\Filament\Admin\Clusters\UserManagement\Resources\Roles\Schemas\RoleForm;
use App\Filament\Admin\Clusters\UserManagement\Resources\Roles\Tables\RolesTable;
use App\Filament\Admin\Clusters\UserManagement\UserManagement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $cluster = UserManagement::class;

    protected static ?int $navigationSort = 20;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CheckBadge;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->position === 'superadmin';
    }

    public static function canView($record): bool
    {
        return auth()->user()->position === 'superadmin';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->position === 'superadmin';
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->position === 'superadmin';
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->position === 'superadmin';
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->position === 'superadmin';
    }
}
