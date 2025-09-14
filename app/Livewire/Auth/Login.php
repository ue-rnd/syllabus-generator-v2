<?php

namespace App\Livewire\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.main-layout')]
class Login extends Controller {
    public function showLoginForm() {
        return view('livewire.auth.login');
    }

    public function authenticate(Request $request) {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }
        $request->session()->regenerate();
        return redirect()->intended(route('home'));

    }
}
