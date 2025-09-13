<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Users\Tables;

use App\Constants\UserConstants;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')
                    ->label('Position')
                    ->badge()
                    ->color(fn ($state): string => is_string($state) ? UserConstants::getPositionColor($state) : 'gray')
                    ->formatStateUsing(fn ($state): string => is_string($state) ? (UserConstants::getPositionOptions()[$state] ?? ucfirst(str_replace('_', ' ', $state))) : 'Not specified')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'superadmin' => 'danger',
                        'admin' => 'warning',
                        'faculty' => 'success',
                        default => 'gray',
                    }),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Deleted')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (User $record) {
                        $currentUserId = Auth::id();
                        
                        if ($record->id === $currentUserId) {
                            Notification::make()
                                ->title('Cannot Delete Own Account')
                                ->body('You cannot delete your own account.')
                                ->warning()
                                ->send();
                            
                            return false;
                        }
                        
                        // Check if trying to delete the last superadmin
                        if ($record->hasRole('superadmin')) {
                            $remainingSuperadmins = User::role('superadmin')->where('id', '!=', $record->id)->count();
                            if ($remainingSuperadmins < 1) {
                                Notification::make()
                                    ->title('Cannot Delete Last Superadmin')
                                    ->body('You cannot delete the last superadmin account. At least one superadmin must remain in the system.')
                                    ->danger()
                                    ->send();
                                
                                return false;
                            }
                        }
                    }),
                ForceDeleteAction::make()
                    ->before(function (User $record) {
                        $currentUserId = Auth::id();
                        
                        if ($record->id === $currentUserId) {
                            Notification::make()
                                ->title('Cannot Delete Own Account')
                                ->body('You cannot permanently delete your own account.')
                                ->warning()
                                ->send();
                            
                            return false;
                        }
                    }),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            $currentUserId = Auth::id();
                            $selfSelected = $records->contains('id', $currentUserId);
                            
                            if ($selfSelected) {
                                Notification::make()
                                    ->title('Cannot Delete Own Account')
                                    ->body('You cannot delete your own account. Please remove your account from the selection.')
                                    ->warning()
                                    ->send();
                                
                                return false;
                            }
                            
                            // Check if trying to delete the last superadmin
                            $superadminRecords = $records->filter(fn ($record) => $record->hasRole('superadmin'));
                            if ($superadminRecords->count() > 0) {
                                $remainingSuperadmins = User::role('superadmin')->whereNotIn('id', $records->pluck('id'))->count();
                                if ($remainingSuperadmins < 1) {
                                    Notification::make()
                                        ->title('Cannot Delete Last Superadmin')
                                        ->body('You cannot delete all superadmin accounts. At least one superadmin must remain in the system.')
                                        ->danger()
                                        ->send();
                                    
                                    return false;
                                }
                            }
                        }),
                    ForceDeleteBulkAction::make()
                        ->before(function ($records) {
                            $currentUserId = Auth::id();
                            $selfSelected = $records->contains('id', $currentUserId);
                            
                            if ($selfSelected) {
                                Notification::make()
                                    ->title('Cannot Delete Own Account')
                                    ->body('You cannot permanently delete your own account. Please remove your account from the selection.')
                                    ->warning()
                                    ->send();
                                
                                return false;
                            }
                        }),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
