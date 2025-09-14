<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\RolePermissionManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    
    // Role and Permission Management
    Route::get('roles', RolePermissionManager::class)
        ->middleware('can:view roles')
        ->name('roles.index');
});

Route::middleware('auth')->group(function () {

    Route::get('home', function() {
        return view('livewire.client.dashboard.home');
    })->name('dashboard');

    Route::get('profile', function(){
        return view('livewire.client.dashboard.profile');
    })->name('profile');

    Route::get('notifications', function(){
        return view('livewire.client.dashboard.notifications');
    })->name('notifications');

    Route::get('bookmarks', function(){
        return view('livewire.client.dashboard.bookmarks');
    })->name('bookmarks');

});


Route::get("forgot_password", function() {
    return view('livewire.auth.forgot-password');
})->name('forgot_password');

require __DIR__.'/auth.php';
