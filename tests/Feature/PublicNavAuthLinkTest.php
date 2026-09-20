<?php

use App\Models\Course;
use App\Models\User;

test('a guest sees the login link and not the learning link on the home', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSeeHtmlInOrder(['<nav', 'href="'.route('login').'"', '</nav>']);
    $response->assertDontSee(route('learning.index'));
});

test('a guest sees the login link and not the learning link on the catalog', function () {
    $response = $this->get(route('courses.index'));

    $response->assertOk();
    $response->assertSeeHtmlInOrder(['<nav', 'href="'.route('login').'"', '</nav>']);
    $response->assertDontSee(route('learning.index'));
});

test('an authenticated user sees the learning link and not the login link on the home', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk();
    $response->assertSeeHtmlInOrder(['<nav', 'href="'.route('learning.index').'"', 'Mi cuenta', '</nav>']);
    $response->assertDontSee(route('login'));
});

test('an authenticated user sees the learning link and not the login link on the catalog', function () {
    $user = User::factory()->create();
    Course::factory()->create(['is_published' => true]);

    $response = $this->actingAs($user)->get(route('courses.index'));

    $response->assertOk();
    $response->assertSeeHtmlInOrder(['<nav', 'href="'.route('learning.index').'"', 'Mi cuenta', '</nav>']);
    $response->assertDontSee(route('login'));
});
