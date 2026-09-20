<?php

use App\Models\User;

test('the site navbar links are visible on the private account page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('learning.index'));

    $response->assertOk();
    $response->assertSee('Cursos');
    $response->assertSee('Categorías');
    $response->assertSee('Cómo funciona');
    $response->assertSee('Nosotras');
});

test('the brand logo appears only once on the private account page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('learning.index'));

    $response->assertOk();
    expect(substr_count($response->getContent(), 'logo-espresso.png'))->toBe(1);
});
