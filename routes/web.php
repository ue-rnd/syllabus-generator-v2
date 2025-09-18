<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\RolePermissionManager;
use App\Livewire\Client\Dashboard\Home;
use App\Livewire\Client\Dashboard\Profile as DashboardProfile;
use App\Livewire\Client\Dashboard\Notifications;
use App\Livewire\Client\Dashboard\Bookmarks;
use App\Livewire\Client\Syllabi\CreateSyllabus;
use App\Livewire\Client\Syllabi\EditSyllabus as ClientEditSyllabus;
use App\Livewire\Client\Syllabi\ViewSyllabus as ClientViewSyllabus;
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

    // PDF Routes
    Route::get('syllabus/{syllabus}/pdf/view', [\App\Http\Controllers\SyllabusPdfController::class, 'view'])
        ->middleware('auth')
        ->name('syllabus.pdf.view');
    Route::get('syllabus/{syllabus}/pdf/download', [\App\Http\Controllers\SyllabusPdfController::class, 'download'])
        ->middleware('auth')
        ->name('syllabus.pdf.download');
});

Route::middleware('auth')->group(function () {

    Route::get('home', Home::class)->name('home');

    Route::get('syllabus/create', CreateSyllabus::class)->name('syllabus');
    Route::get('syllabus/{syllabus}', ClientViewSyllabus::class)->name('syllabus.view');
    Route::get('syllabus/{syllabus}/edit', ClientEditSyllabus::class)->name('syllabus.edit');

    Route::get('profile', DashboardProfile::class)->name('profile');

    Route::get('notifications', Notifications::class)->name('notifications');

    Route::get('bookmarks', Bookmarks::class)->name('bookmarks');
});



require __DIR__.'/auth.php';
