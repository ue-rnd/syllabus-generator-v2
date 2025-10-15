<?php

namespace App\Livewire\Client\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('livewire.client.dashboard.base')]
class Profile extends Component
{
    public string $firstname = '';

    public string $lastname = '';

    public string $middlename = '';

    public string $email = '';

    public bool $saved = false;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    protected function user(): User
    {
        return Auth::user();
    }

    public function mount(): void
    {
        $user = $this->user();
        $this->firstname = $user->firstname ?? '';
        $this->lastname = $user->lastname ?? '';
        $this->middlename = $user->middlename ?? '';
        $this->email = $user->email ?? '';
    }

    public function updateProfileInformation(): void
    {
        $user = $this->user();

        $validated = $this->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ]);

        $name = $validated['firstname'];
        if (! empty($validated['middlename'])) {
            $name .= ' '.$validated['middlename'];
        }
        $name .= ' '.$validated['lastname'];

        $validated['name'] = $name;

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $user->refresh();
        \Illuminate\Support\Facades\Auth::setUser($user);

        $this->firstname = $user->firstname ?? '';
        $this->lastname = $user->lastname ?? '';
        $this->middlename = $user->middlename ?? '';
        $this->email = $user->email ?? '';

        $this->saved = true;

        session()->flash('status', 'profile-updated');
        $this->dispatch('profile-updated', name: $user->name);
    }

    public function updatePassword(): void
    {
        $this->resetErrorBag();

        $validated = $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = $this->user();
        $user->password = $validated['password'];
        $user->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('status', 'password-updated');
        $this->dispatch('password-updated');
    }

    public function render()
    {
        return view('livewire.client.dashboard.profile');
    }
}
