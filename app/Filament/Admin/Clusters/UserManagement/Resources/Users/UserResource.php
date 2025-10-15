<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Users;

use App\Filament\Admin\Clusters\UserManagement\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Clusters\UserManagement\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Clusters\UserManagement\Resources\Users\Pages\ListUsers;
use App\Filament\Admin\Clusters\UserManagement\Resources\Users\Pages\ViewUser;
use App\Filament\Admin\Clusters\UserManagement\Resources\Users\Schemas\UserForm;
use App\Filament\Admin\Clusters\UserManagement\Resources\Users\Schemas\UserInfolist;
use App\Filament\Admin\Clusters\UserManagement\Resources\Users\Tables\UsersTable;
use App\Filament\Admin\Clusters\UserManagement\UserManagement;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $cluster = UserManagement::class;

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view users');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view users');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create users');
    }

    public static function canEdit($record): bool
    {
        // Users can edit their own profile, or if they have edit users permission
        return auth()->user()->can('edit users') || auth()->id() === $record->id;
    }

    public static function canDelete($record): bool
    {
        // Cannot delete yourself or if you don't have permission
        return auth()->user()->can('delete users') && auth()->id() !== $record->id;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete users');
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        // If user is superadmin, show all users
        if ($user->isSuperAdmin()) {
            return $query->withoutGlobalScopes([SoftDeletingScope::class]);
        }

        // If user has view users permission, show users based on their scope
        if ($user->can('view users')) {
            // Deans and Associate Deans can see users from their colleges
            if (in_array($user->position, ['dean', 'associate_dean'])) {
                $collegeIds = $user->getAccessibleColleges()->pluck('id');
                return $query->whereIn('college_id', $collegeIds)
                    ->orWhere('id', $user->id)
                    ->withoutGlobalScopes([SoftDeletingScope::class]);
            }

            // Department Chairs can see users from their departments
            if ($user->position === 'department_chair') {
                $departmentIds = $user->getAccessibleDepartments()->pluck('id');
                return $query->whereIn('department_id', $departmentIds)
                    ->orWhere('id', $user->id)
                    ->withoutGlobalScopes([SoftDeletingScope::class]);
            }

            // Other users with view permission can see all active users
            return $query->withoutGlobalScopes([SoftDeletingScope::class]);
        }

        // Default: users can only see themselves
        return $query->where('id', $user->id);
    }
}
