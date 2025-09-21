<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Permissions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function ($record) {
                        $protectedPermissions = [
                            'view dashboard',
                            'view users',
                            'create users',
                            'edit users',
                            'delete users',
                            'view roles',
                            'create roles',
                            'edit roles',
                            'delete roles',
                            'assign roles',
                            'view permissions',
                            'create permissions',
                            'edit permissions',
                            'delete permissions',
                            'assign permissions',
                        ];

                        if (in_array($record->name, $protectedPermissions)) {
                            Notification::make()
                                ->title('Cannot Delete Essential Permission')
                                ->body("The '{$record->name}' permission is essential for system operation and cannot be deleted.")
                                ->warning()
                                ->send();

                            return false;
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            $protectedPermissions = [
                                'view dashboard',
                                'view users',
                                'create users',
                                'edit users',
                                'delete users',
                                'view roles',
                                'create roles',
                                'edit roles',
                                'delete roles',
                                'assign roles',
                                'view permissions',
                                'create permissions',
                                'edit permissions',
                                'delete permissions',
                                'assign permissions',
                            ];

                            $protectedRecords = $records->filter(fn ($record) => in_array($record->name, $protectedPermissions));

                            if ($protectedRecords->count() > 0) {
                                $permissionNames = $protectedRecords->pluck('name')->join(', ');
                                Notification::make()
                                    ->title('Cannot Delete Essential Permissions')
                                    ->body("The following permissions are essential for system operation and cannot be deleted: {$permissionNames}")
                                    ->warning()
                                    ->send();

                                return false;
                            }
                        }),
                ]),
            ]);
    }
}
