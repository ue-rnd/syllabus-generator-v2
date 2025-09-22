<?php

namespace App\Filament\Admin\Pages;

use App\Models\College;
use App\Models\Department;
use App\Services\AuthSecurityService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\JsonEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileManagement extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;

    protected string $view = 'filament.admin.pages.profile-management';

    protected static ?string $navigationLabel = 'My Profile';

    protected static ?string $title = 'Profile Management';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();

        $this->form->fill([
            'firstname' => $user->firstname,
            'middlename' => $user->middlename,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'phone' => $user->phone,
            'birth_date' => $user->birth_date,
            'address' => $user->address,
            'bio' => $user->bio,
            'avatar' => $user->avatar,
            'emergency_contact' => $user->emergency_contact ?? [],
            'timezone' => $user->timezone,
            'locale' => $user->locale,
            'preferences' => $user->preferences ?? [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Profile')
                    ->tabs([
                        Tabs\Tab::make('Personal Information')
                            ->schema([
                                Section::make('Basic Information')
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

                                        DatePicker::make('birth_date')
                                            ->label('Date of Birth')
                                            ->native(false),

                                        Textarea::make('address')
                                            ->label('Address')
                                            ->rows(3),

                                        Textarea::make('bio')
                                            ->label('Biography')
                                            ->rows(4)
                                            ->maxLength(1000),

                                        FileUpload::make('avatar')
                                            ->label('Profile Picture')
                                            ->image()
                                            ->avatar()
                                            ->imageEditor()
                                            ->circleCropper()
                                            ->directory('user-avatars'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Emergency Contacts')
                            ->schema([
                                Section::make('Emergency Contact Information')
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
                                    ]),
                            ]),

                        Tabs\Tab::make('Preferences')
                            ->schema([
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
                                                    ])
                                                    ->default('en'),
                                            ]),

                                        JsonEditor::make('preferences')
                                            ->label('Additional Preferences')
                                            ->helperText('JSON format for storing user-specific settings')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Professional Info')
                            ->schema([
                                Section::make('Professional Information')
                                    ->description('This information is managed by administrators and cannot be edited.')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('position_display')
                                                    ->label('Academic Position')
                                                    ->default(fn () => Auth::user()->getPositionTitleAttribute())
                                                    ->disabled(),

                                                TextInput::make('employment_type_display')
                                                    ->label('Employment Type')
                                                    ->default(fn () => Auth::user()->employment_type ? ucwords(str_replace('_', ' ', Auth::user()->employment_type)) : 'Not Set')
                                                    ->disabled(),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('college_display')
                                                    ->label('College')
                                                    ->default(fn () => Auth::user()->college?->name ?? 'Not Assigned')
                                                    ->disabled(),

                                                TextInput::make('department_display')
                                                    ->label('Department')
                                                    ->default(fn () => Auth::user()->department?->name ?? 'Not Assigned')
                                                    ->disabled(),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('hire_date_display')
                                                    ->label('Hire Date')
                                                    ->default(fn () => Auth::user()->hire_date?->format('M j, Y') ?? 'Not Set')
                                                    ->disabled(),

                                                TextInput::make('employee_id_display')
                                                    ->label('Employee ID')
                                                    ->default(fn () => Auth::user()->employee_id ?? 'Not Set')
                                                    ->disabled(),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $user = Auth::user();

            // Update user data
            $user->update([
                'firstname' => $data['firstname'],
                'middlename' => $data['middlename'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'birth_date' => $data['birth_date'],
                'address' => $data['address'],
                'bio' => $data['bio'],
                'avatar' => $data['avatar'],
                'emergency_contact' => $data['emergency_contact'],
                'timezone' => $data['timezone'],
                'locale' => $data['locale'],
                'preferences' => $data['preferences'],
            ]);

            // Log security event
            app(AuthSecurityService::class)->logSecurityEvent('Profile updated', $user);

            Notification::make()
                ->title('Profile Updated')
                ->body('Your profile has been updated successfully.')
                ->success()
                ->send();

        } catch (Halt $exception) {
            Notification::make()
                ->title('Update Failed')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save')
                ->color('primary'),

            Action::make('changePassword')
                ->label('Change Password')
                ->color('warning')
                ->url('/admin/change-password'),

            Action::make('resetToDefaults')
                ->label('Reset to Defaults')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $user = Auth::user();

                    $this->form->fill([
                        'timezone' => 'UTC',
                        'locale' => 'en',
                        'preferences' => [],
                    ]);

                    Notification::make()
                        ->title('Preferences Reset')
                        ->body('Your preferences have been reset to defaults.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTitle(): string
    {
        return 'My Profile';
    }

    public function getHeading(): string
    {
        return 'Profile Management';
    }

    public function getSubheading(): ?string
    {
        return 'Manage your personal information and preferences.';
    }
}