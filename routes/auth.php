<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Auth\ConfirmPassword;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [Login::class, 'render'])->name('showLoginForm');
    Route::post('login', [Login::class, 'authenticate'])->name('login');
    Route::get('forgot-password', [ForgotPassword::class, 'render'])->name('password.request');
    Route::post('forgot-password', [ForgotPassword::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPassword::class, 'render'])->name('password.reset');
    Route::post('reset-password/{token}', [ResetPassword::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', VerifyEmail::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', ConfirmPassword::class)
        ->name('password.confirm');
});

Route::get('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');
