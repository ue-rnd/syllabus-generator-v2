<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


#[Layout('components.layouts.app.main-layout')]
class ForgotPassword extends Controller {
    public function render(){
        return view('livewire.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request) {
        $request->validate(['email' => 'required|email']);

        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            $token = \Illuminate\Support\Str::random(60);

            \DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                ['token' => $token, 'created_at' => now()]
            );

            $resetLink = url("/reset-password/{$token}");
            \Mail::raw("Click here to reset your password: $resetLink", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Password Reset Link');
            });
            
        }

        session()->flash('success', 'If the email address exists in our records, you will receive a password reset link shortly.');
        return redirect()->route('password.request');
    }
}
