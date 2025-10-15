<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Users\Schemas;

use App\Constants\UserConstants;
use App\Models\College;
use App\Models\Department;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Personal Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('firstname')
                                    ->label('First Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('middlename')
                                    ->label('Middle Name')
                                    ->maxLength(255),

                                TextInput::make('lastname')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('birth_date')
                                    ->label('Date of Birth')
                                    ->native(false),

                                TextInput::make('employee_id')
                                    ->label('Employee ID')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),
                            ]),

                        Textarea::make('address')
                            ->label('Address')
                            ->rows(3),

                        FileUpload::make('avatar')
                            ->label('Profile Picture')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->directory('user-avatars'),
                    ]),

                Section::make('Professional Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('position')
                                    ->label('Academic Position')
                                    ->options(UserConstants::getPositionOptions())
                                    ->searchable()
                                    ->placeholder('Select a position'),

                                Select::make('employment_type')
                                    ->label('Employment Type')
                                    ->options(UserConstants::getEmploymentTypeOptions())
                                    ->default('full_time'),
                            ]),

                        TextInput::make('title')
                            ->label('Job Title')
                            ->maxLength(255)
                            ->helperText('Custom job title (overrides academic position)'),

                        Textarea::make('bio')
                            ->label('Biography')
                            ->rows(4)
                            ->maxLength(1000),

                        DatePicker::make('hire_date')
                            ->label('Hire Date')
                            ->native(false),
                    ]),

                Section::make('Organizational Assignment')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('college_id')
                                    ->label('College')
                                    ->options(College::active()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (callable $set) => $set('department_id', null)),

                                Select::make('department_id')
                                    ->label('Department')
                                    ->options(fn (callable $get) => Department::where('college_id', $get('college_id'))->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (callable $get) => ! $get('college_id')),
                            ]),
                    ]),

                Section::make('Emergency Contact')
                    ->schema([
                        Repeater::make('emergency_contact')
                            ->label('Emergency Contacts')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('relationship')
                                            ->required()
                                            ->maxLength(100),

                                        TextInput::make('phone')
                                            ->tel()
                                            ->required()
                                            ->maxLength(20),
                                    ]),

                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),

                                Textarea::make('address')
                                    ->rows(2),
                            ])
                            ->addActionLabel('Add Emergency Contact')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->maxItems(3),
                    ])
                    ->collapsible(),

                Section::make('Security & Access')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('roles')
                                    ->label('System Role')
                                    ->relationship('roles', 'name')
                                    ->options(Role::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->multiple(false)
                                    ->helperText('Defines what the user can access in the system'),

                                Toggle::make('is_active')
                                    ->label('Active Account')
                                    ->default(true)
                                    ->helperText('Inactive users cannot log in'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('must_change_password')
                                    ->label('Must Change Password')
                                    ->helperText('User will be forced to change password on next login'),

                                Toggle::make('two_factor_enabled')
                                    ->label('Two-Factor Authentication')
                                    ->disabled()
                                    ->helperText('Managed through user profile settings'),
                            ]),
                    ]),

                Section::make('User Preferences')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('timezone')
                                    ->label('Timezone')
                                    ->options(collect(timezone_identifiers_list())->mapWithKeys(fn ($tz) => [$tz => $tz]))
                                    ->default('UTC')
                                    ->searchable(),

                                Select::make('locale')
                                    ->label('Language')
                                    ->options([
                                        'en' => 'English',
                                        'es' => 'Spanish',
                                        'fr' => 'French',
                                        // Add more languages as needed
                                    ])
                                    ->default('en'),
                            ]),

                        KeyValue::make('preferences')
                            ->label('Additional Preferences')
                            ->helperText('JSON format for storing user-specific settings')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
