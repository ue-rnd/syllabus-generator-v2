<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(2)
                    ->schema([
                        Group::make([
                            Section::make('Personal Information')
                                ->schema([
                                    ImageEntry::make('avatar')
                                        ->label('Profile Picture')
                                        ->circular()
                                        ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=6366f1&color=fff')
                                        ->imageSize(100),

                                    TextEntry::make('name')
                                        ->label('Full Name')

                                        ->weight('bold'),

                                    TextEntry::make('email')
                                        ->label('Email Address')
                                        ->icon('heroicon-o-envelope')
                                        ->copyable(),

                                    TextEntry::make('phone')
                                        ->label('Phone Number')
                                        ->icon('heroicon-o-phone')
                                        ->placeholder('Not provided'),

                                    TextEntry::make('birth_date')
                                        ->label('Date of Birth')
                                        ->date()
                                        ->placeholder('Not provided'),

                                    TextEntry::make('address')
                                        ->label('Address')
                                        ->placeholder('Not provided')
                                        ->limit(100),

                                    TextEntry::make('bio')
                                        ->label('Biography')
                                        ->placeholder('No biography provided')
                                        ->limit(200),
                                ])
                                ->columns(1),
                        ]),

                        Group::make([
                            Section::make('Professional Information')
                                ->schema([
                                    TextEntry::make('employee_id')
                                        ->label('Employee ID')
                                        ->placeholder('Not assigned'),

                                    TextEntry::make('position')
                                        ->label('Academic Position')
                                        ->formatStateUsing(fn ($state, $record) => $record->getPositionTitleAttribute())
                                        ->badge()
                                        ->color(fn ($state) => match ($state) {
                                            'superadmin' => 'danger',
                                            'dean', 'associate_dean' => 'warning',
                                            'department_chair' => 'info',
                                            'qa_representative' => 'success',
                                            'faculty' => 'primary',
                                            default => 'gray',
                                        }),

                                    TextEntry::make('employment_type')
                                        ->label('Employment Type')
                                        ->formatStateUsing(fn ($state) => $state ? ucwords(str_replace('_', ' ', $state)) : 'Not set')
                                        ->badge(),

                                    TextEntry::make('title')
                                        ->label('Job Title')
                                        ->placeholder('Uses academic position'),

                                    TextEntry::make('college.name')
                                        ->label('College')
                                        ->placeholder('Not assigned'),

                                    TextEntry::make('department.name')
                                        ->label('Department')
                                        ->placeholder('Not assigned'),

                                    TextEntry::make('hire_date')
                                        ->label('Hire Date')
                                        ->date()
                                        ->placeholder('Not set'),
                                ])
                                ->columns(1),
                        ]),
                    ]),

                Section::make('Emergency Contacts')
                    ->schema([
                        RepeatableEntry::make('emergency_contact')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Name'),

                                        TextEntry::make('relationship')
                                            ->label('Relationship'),

                                        TextEntry::make('phone')
                                            ->label('Phone')
                                            ->icon('heroicon-o-phone'),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('email')
                                            ->label('Email')
                                            ->icon('heroicon-o-envelope')
                                            ->placeholder('Not provided'),

                                        TextEntry::make('address')
                                            ->label('Address')
                                            ->placeholder('Not provided'),
                                    ]),
                            ])
                            ->placeholder('No emergency contacts registered'),
                    ])
                    ->collapsible(),

                Section::make('Security & Access')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                IconEntry::make('is_active')
                                    ->label('Account Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                TextEntry::make('two_factor_enabled')
                                    ->label('Two-Factor Auth')
                                    ->formatStateUsing(fn ($state) => $state ? 'Enabled' : 'Disabled')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'warning'),

                                TextEntry::make('must_change_password')
                                    ->label('Password Status')
                                    ->formatStateUsing(fn ($state) => $state ? 'Must Change' : 'Current')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'danger' : 'success'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('roles')
                                    ->label('System Roles')
                                    ->formatStateUsing(fn ($state) => $state ? $state->pluck('name')->map(fn ($role) => ucwords(str_replace('_', ' ', $role)))->join(', ') : 'No roles assigned')
                                    ->placeholder('No roles assigned'),

                                TextEntry::make('primary_role')
                                    ->label('Primary Role')
                                    ->formatStateUsing(fn ($state, $record) => ucwords(str_replace('_', ' ', $record->getPrimaryRoleAttribute())))
                                    ->badge()
                                    ->color('primary'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('last_login_at')
                                    ->label('Last Login')
                                    ->dateTime()
                                    ->placeholder('Never logged in'),

                                TextEntry::make('last_login_ip')
                                    ->label('Last Login IP')
                                    ->placeholder('Unknown'),

                                TextEntry::make('login_attempts')
                                    ->label('Failed Attempts')
                                    ->badge()
                                    ->color(fn ($state) => $state > 3 ? 'danger' : ($state > 0 ? 'warning' : 'success')),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('locked_until')
                                    ->label('Account Locked Until')
                                    ->dateTime()
                                    ->placeholder('Not locked')
                                    ->color('danger'),

                                TextEntry::make('password_changed_at')
                                    ->label('Password Last Changed')
                                    ->dateTime()
                                    ->placeholder('Never changed'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Preferences & Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('timezone')
                                    ->label('Timezone')
                                    ->placeholder('UTC'),

                                TextEntry::make('locale')
                                    ->label('Language')
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'en' => 'English',
                                        'es' => 'Spanish',
                                        'fr' => 'French',
                                        default => ucfirst($state ?? 'English'),
                                    }),
                            ]),

                        TextEntry::make('preferences')
                            ->label('Additional Preferences')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : 'No custom preferences')
                            ->placeholder('No custom preferences'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Account Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Account Created')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime(),

                                TextEntry::make('email_verified_at')
                                    ->label('Email Verified')
                                    ->dateTime()
                                    ->placeholder('Not verified'),
                            ]),

                        TextEntry::make('deleted_at')
                            ->label('Account Deleted')
                            ->dateTime()
                            ->visible(fn (User $record): bool => $record->trashed()),
                    ])
                    ->collapsible()
                    ->collapsed(),

            ]);
    }
}
