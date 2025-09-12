<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});


Route::get('home', function() {
    return view('livewire.client.dashboard.home');
})->name('dashboard_home');

Route::get('profile', function(){
    return view('livewire.client.dashboard.profile');
})->name('profile');

Route::get('notifications', function(){
    return view('livewire.client.dashboard.notifications');
})->name('notifications');

Route::get('bookmarks', function(){
    return view('livewire.client.dashboard.bookmarks');
})->name('bookmarks');

require __DIR__.'/auth.php';
