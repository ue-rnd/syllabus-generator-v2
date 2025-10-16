<?php

namespace App\Filament\Admin\Pages;

use App\Models\User;
use App\Services\AuthSecurityService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

/**
 * @property mixed $form
 */
class ProfileManagement extends Page
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;

    protected string $view = 'filament.admin.pages.profile-management';

    protected static ?string $navigationLabel = 'My Profile';

    protected static ?string $title = 'Profile Management';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public User $user;

    public function mount(): void
    {
        $this->user = Auth::user();

        $this->form->fill([
            'firstname' => $this->user->firstname ?? '',
            'middlename' => $this->user->middlename ?? '',
            'lastname' => $this->user->lastname ?? '',
            'email' => $this->user->email ?? '',
            'phone' => $this->user->phone ?? '',
            'title' => $this->user->title ?? '',
            'birth_date' => $this->user->birth_date,
            'address' => $this->user->address ?? '',
            'bio' => $this->user->bio ?? '',
            'avatar' => $this->user->avatar,
            'emergency_contact' => $this->user->emergency_contact ?? [],
            'emergency_phone' => $this->user->emergency_phone ?? '',
            'timezone' => $this->user->timezone ?? 'UTC',
            'locale' => $this->user->locale ?? 'en',
            'preferences' => $this->user->preferences ?? [],
        ]);
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Personal Information')
                ->description('Update your basic profile information')
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

                    Grid::make(3)
                        ->schema([
                            TextInput::make('email')
                                ->label('Email Address')
                                ->email()
                                ->required()
                                ->unique('users', 'email', fn () => Auth::user())
                                ->maxLength(255),

                            TextInput::make('phone')
                                ->label('Phone Number')
                                ->tel()
                                ->maxLength(20),

                            TextInput::make('title')
                                ->label('Job Title')
                                ->maxLength(255),
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

            Section::make('Emergency Contact Information')
                ->description('Add emergency contacts for safety purposes')
                ->schema([
                    TextInput::make('emergency_phone')
                        ->label('Primary Emergency Phone')
                        ->tel()
                        ->maxLength(20)
                        ->helperText('A quick contact number for emergencies'),

                    Repeater::make('emergency_contact')
                        ->label('Detailed Emergency Contacts')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Full Name')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('relationship')
                                        ->label('Relationship')
                                        ->required()
                                        ->maxLength(100)
                                        ->placeholder('e.g., Spouse, Parent, Sibling'),

                                    TextInput::make('phone')
                                        ->label('Phone Number')
                                        ->tel()
                                        ->required()
                                        ->maxLength(20),
                                ]),

                            Grid::make(2)
                                ->schema([
                                    TextInput::make('email')
                                        ->label('Email Address')
                                        ->email()
                                        ->maxLength(255),

                                    Textarea::make('address')
                                        ->label('Address')
                                        ->rows(2),
                                ]),
                        ])
                        ->addActionLabel('Add Emergency Contact')
                        ->reorderableWithButtons()
                        ->collapsible()
                        ->maxItems(5)
                        ->defaultItems(0),
                ]),

            Section::make('User Preferences')
                ->description('Customize your account settings and preferences')
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

                    KeyValue::make('preferences')
                        ->label('Additional Preferences')
                        ->helperText('Key-value pairs for storing user-specific settings')
                        ->columnSpanFull(),
                ]),

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
                                ->default(fn () => Auth::user()->college->name ?? 'Not Assigned')
                                ->disabled(),

                            TextInput::make('department_display')
                                ->label('Department')
                                ->default(fn () => Auth::user()->department->name ?? 'Not Assigned')
                                ->disabled(),
                        ]),

                    Grid::make(2)
                        ->schema([
                            TextInput::make('hire_date_display')
                                ->label('Hire Date')
                                ->default(fn () => Auth::user()->hire_date ? \Carbon\Carbon::parse(Auth::user()->hire_date)->format('M j, Y') : 'Not Set')
                                ->disabled(),

                            TextInput::make('employee_id_display')
                                ->label('Employee ID')
                                ->default(fn () => Auth::user()->employee_id ?? 'Not Set')
                                ->disabled(),
                        ]),
                ]),
        ];
    }

    public function save(): void
    {
        try {
            // Use the form state to ensure file uploads and other components are properly dehydrated
            $data = $this->form->getState();
            $user = Auth::user();

            // Update user data (only editable fields)
            $user->update([
                'firstname' => $data['firstname'],
                'middlename' => $data['middlename'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'title' => $data['title'],
                'birth_date' => $data['birth_date'],
                'address' => $data['address'],
                'bio' => $data['bio'],
                'avatar' => $data['avatar'] ?? null,
                'emergency_contact' => $data['emergency_contact'] ?? [],
                'emergency_phone' => $data['emergency_phone'],
                'timezone' => $data['timezone'],
                'locale' => $data['locale'],
                'preferences' => $data['preferences'] ?? [],
            ]);

            // Refresh the user instance
            $this->user = $user->fresh();

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
        } catch (\Exception $exception) {
            Notification::make()
                ->title('Update Failed')
                ->body('An unexpected error occurred while updating your profile.')
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
                ->label('Reset Preferences')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Reset User Preferences')
                ->modalDescription('This will reset your timezone, language, and custom preferences to default values. Your personal information will not be affected.')
                ->action(function () {
                    $this->data['timezone'] = 'UTC';
                    $this->data['locale'] = 'en';
                    $this->data['preferences'] = [];

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
