<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\RolePermissionManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('livewire.auth.login');
})->name('home');


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    
    // Role and Permission Management
    Route::get('roles', RolePermissionManager::class)
        ->middleware('can:view roles')
        ->name('roles.index');

    // PDF Routes
    Route::get('syllabus/{syllabus}/pdf/view', [\App\Http\Controllers\SyllabusPdfController::class, 'view'])
        ->middleware('auth')
        ->name('syllabus.pdf.view');
    Route::get('syllabus/{syllabus}/pdf/download', [\App\Http\Controllers\SyllabusPdfController::class, 'download'])
        ->middleware('auth')
        ->name('syllabus.pdf.download');
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


Route::get("forgot_password", function() {
    return view('livewire.auth.forgot-password');
})->name('forgot_password');

require __DIR__.'/auth.php';
