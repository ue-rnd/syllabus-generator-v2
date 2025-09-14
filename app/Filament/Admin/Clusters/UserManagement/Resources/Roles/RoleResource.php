<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Roles;

use App\Filament\Admin\Clusters\UserManagement\UserManagement;
use App\Filament\Admin\Clusters\UserManagement\Resources\Roles\Pages\CreateRole;
use App\Filament\Admin\Clusters\UserManagement\Resources\Roles\Pages\EditRole;
use App\Filament\Admin\Clusters\UserManagement\Resources\Roles\Pages\ListRoles;
use App\Filament\Admin\Clusters\UserManagement\Resources\Roles\Schemas\RoleForm;
use App\Filament\Admin\Clusters\UserManagement\Resources\Roles\Tables\RolesTable;
use Spatie\Permission\Models\Role;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

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
}
