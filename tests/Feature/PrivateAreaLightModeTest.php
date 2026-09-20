<?php

use App\Models\User;

test('the appearance settings page no longer exists', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/settings/appearance')->assertNotFound();
});

test('the settings menu no longer shows appearance', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertOk();
    $response->assertDontSee('Apariencia');
});

test('the private account page does not force dark mode', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('learning.index'));

    $response->assertOk();
    $response->assertDontSee('class="dark"', false);
});

test('the settings profile page does not force dark mode', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertOk();
    $response->assertDontSee('class="dark"', false);
});

test('the login page does not force dark mode', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertDontSee('class="dark"', false);
});
