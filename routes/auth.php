<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ResetPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [Login::class, 'render'])->name('showLoginForm');
    Route::post('login', [Login::class, 'authenticate'])->name('login');
    Route::get('forgot-password', [ForgotPassword::class, 'render'])->name('password.request');
    Route::post('forgot-password', [ForgotPassword::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPassword::class, 'render'])->name('password.reset');
    Route::post('reset-password/{token}', [ResetPassword::class, 'reset'])->name('password.update');
});

Route::get('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');
