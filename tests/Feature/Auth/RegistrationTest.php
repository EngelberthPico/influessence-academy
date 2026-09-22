<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Passw0rd!',
        'password_confirmation' => 'Passw0rd!',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('registering a new account sends the email verification notification', function () {
    Notification::fake();

    $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Passw0rd!',
        'password_confirmation' => 'Passw0rd!',
    ]);

    $user = User::whereEmail('test@example.com')->firstOrFail();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('registration fails with a password that does not meet the complexity requirements', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'lowercase',
        'password_confirmation' => 'lowercase',
    ]);

    $response->assertSessionHasErrors('password');

    $this->assertGuest();
});
