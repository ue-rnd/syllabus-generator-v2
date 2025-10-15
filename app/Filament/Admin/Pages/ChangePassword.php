<?php

namespace App\Filament\Admin\Pages;

use App\Services\AuthSecurityService;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Key;

    protected string $view = 'filament.admin.pages.change-password';

    protected static ?string $title = 'Change Password';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('current_password')
                    ->label('Current Password')
                    ->password()
                    ->required()
                    ->rules(['required'])
                    ->validationAttribute('current password'),

                TextInput::make('password')
                    ->label('New Password')
                    ->password()
                    ->required()
                    ->rules([
                        'required',
                        'confirmed',
                        Password::min(8)
                            ->letters()
                            ->mixedCase()
                            ->numbers()
                            ->symbols(),
                    ])
                    ->validationAttribute('new password')
                    ->helperText('Password must be at least 8 characters long and contain uppercase, lowercase, numbers, and symbols.'),

                TextInput::make('password_confirmation')
                    ->label('Confirm New Password')
                    ->password()
                    ->required()
                    ->validationAttribute('password confirmation'),
            ])
            ->statePath('data');
    }

    public function changePassword(): void
    {
        try {
            $data = $this->form->getState();
            $user = Auth::user();
            $authService = app(AuthSecurityService::class);

            // Verify current password
            if (! Hash::check($data['current_password'], $user->password)) {
                throw new Halt('The current password is incorrect.');
            }

            // Check password strength
            $passwordErrors = $authService->validatePasswordStrength($data['password']);
            if (! empty($passwordErrors)) {
                throw new Halt(implode(' ', $passwordErrors));
            }

            // Check if password was recently used
            if ($authService->isPasswordRecentlyUsed($user, $data['password'])) {
                throw new Halt('You cannot reuse a recent password. Please choose a different password.');
            }

            // Update password
            $user->update([
                'password' => Hash::make($data['password']),
                'password_changed_at' => now(),
                'must_change_password' => false,
            ]);

            // Log security event
            $authService->logSecurityEvent('Password changed successfully', $user);

            Notification::make()
                ->title('Password Changed Successfully')
                ->body('Your password has been updated successfully.')
                ->success()
                ->send();

            // Redirect to dashboard
            $this->redirect('/admin');

        } catch (Halt $exception) {
            Notification::make()
                ->title('Password Change Failed')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getTitle(): string
    {
        return 'Change Password';
    }

    public function getHeading(): string
    {
        $user = Auth::user();

        if ($user->must_change_password) {
            return 'Password Change Required';
        }

        return 'Change Password';
    }

    public function getSubheading(): ?string
    {
        $user = Auth::user();

        if ($user->must_change_password) {
            return 'You must change your password before continuing to use the system.';
        }

        $passwordChangedAt = $user->password_changed_at;
        if ($passwordChangedAt) {
            if (!($passwordChangedAt instanceof \Carbon\Carbon)) {
                $passwordChangedAt = \Carbon\Carbon::parse($passwordChangedAt);
            }
            
            if ($passwordChangedAt->diffInDays(now()) > 60) {
                return 'Your password is over 60 days old. Consider changing it for security.';
            }
        }

        return 'Update your password to keep your account secure.';
    }
}
