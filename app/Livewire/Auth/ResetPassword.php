<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


#[Layout('components.layouts.app')]
class ResetPassword extends Controller{
    public function render(){
        $token = request()->route('token');

        $tokenData = \DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$tokenData || \Carbon\Carbon::parse($tokenData->created_at)->addMinutes(60)->isPast()) {
            Session::flash('error', 'This password reset token is invalid or has expired. Please request a new password reset.');
            return redirect()->route('login');
        }
        return view('livewire.auth.reset-password', compact('token'));
    }

    public function reset(Request $request) {
        $token = $request->route('token');
        $request->validate([
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|min:8|same:new_password',
        ]);

        // check if the token exists and is valid
        $tokenData = \DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$tokenData || \Carbon\Carbon::parse($tokenData->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['token' => 'This password reset token is invalid or has expired.']);
        }

        // change the user's password
        $user = \App\Models\User::where('email', $tokenData->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'No user found for this email address.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();
        event(new PasswordReset($user));

        \DB::table('password_reset_tokens')->where('email', $tokenData->email)->delete();
        Session::flash('success', 'Your password has been reset successfully. You can now log in with your new password.');
        return redirect()->route('showLoginForm');

    }
}
