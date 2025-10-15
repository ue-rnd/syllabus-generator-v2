<?php

use App\Livewire\Auth\Register;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register and are redirected to home', function () {
    $response = Livewire::test(Register::class)
        ->set('firstname', 'Test')
        ->set('lastname', 'User')
        ->set('middlename', 'Middle')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('home', absolute: false));

    $this->assertAuthenticated();

    // Verify the user was created with correct name fields
    $user = \App\Models\User::where('email', 'test@example.com')->first();
    expect($user->firstname)->toBe('Test');
    expect($user->lastname)->toBe('User');
    expect($user->middlename)->toBe('Middle');
    expect($user->name)->toBe('Test Middle User');
});
