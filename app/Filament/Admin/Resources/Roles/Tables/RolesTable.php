<?php

namespace App\Filament\Admin\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions Count'),
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
                        $protectedRoles = ['superadmin', 'admin', 'faculty'];
                        
                        if (in_array($record->name, $protectedRoles)) {
                            Notification::make()
                                ->title('Cannot Delete Essential Role')
                                ->body("The '{$record->name}' role is essential for system operation and cannot be deleted.")
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
                            $protectedRoles = ['superadmin', 'admin', 'faculty'];
                            $protectedRecords = $records->filter(fn ($record) => in_array($record->name, $protectedRoles));
                            
                            if ($protectedRecords->count() > 0) {
                                $roleNames = $protectedRecords->pluck('name')->join(', ');
                                Notification::make()
                                    ->title('Cannot Delete Essential Roles')
                                    ->body("The following roles are essential for system operation and cannot be deleted: {$roleNames}")
                                    ->warning()
                                    ->send();
                                
                                return false;
                            }
                        }),
                ]),
            ]);
    }
}
