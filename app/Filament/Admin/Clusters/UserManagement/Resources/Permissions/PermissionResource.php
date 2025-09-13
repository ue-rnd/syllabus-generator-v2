<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Permissions;

use App\Filament\Admin\Clusters\UserManagement\UserManagement;
use App\Filament\Admin\Clusters\UserManagement\Resources\Permissions\Pages\CreatePermission;
use App\Filament\Admin\Clusters\UserManagement\Resources\Permissions\Pages\EditPermission;
use App\Filament\Admin\Clusters\UserManagement\Resources\Permissions\Pages\ListPermissions;
use App\Filament\Admin\Clusters\UserManagement\Resources\Permissions\Schemas\PermissionForm;
use App\Filament\Admin\Clusters\UserManagement\Resources\Permissions\Tables\PermissionsTable;
use Spatie\Permission\Models\Permission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $cluster = UserManagement::class;

    protected static ?int $navigationSort = 30;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ListBullet;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PermissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermissionsTable::configure($table);
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
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }
}
