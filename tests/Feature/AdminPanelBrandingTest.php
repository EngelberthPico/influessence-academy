<?php

use App\Enums\UserRole;
use App\Models\User;

test('an admin can open the admin panel and the logo links to the public site', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get('/admin/course-accesses');

    $response->assertOk();
    $response->assertSeeHtmlInOrder(['href="'.route('home').'"', 'fi-logo']);
});

test('the admin panel topbar no longer has "Ver sitio" or a link to the public catalog', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get('/admin/course-accesses');

    $response->assertOk();
    $response->assertDontSee('Ver sitio');
    $response->assertDontSee(route('courses.index'), false);
});

test('"Mi cuenta" still points to the student account in the topbar and the avatar menu', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get('/admin/course-accesses');

    $response->assertOk();
    $response->assertSee(route('learning.index'), false);
});

test('the admin panel no longer links to the Filament info widget', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get('/admin/course-accesses');

    $response->assertOk();
    $response->assertDontSee('filamentphp.com', false);
});

test('a non-admin user cannot access the admin panel', function () {
    $user = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($user)->get('/admin')->assertForbidden();
});
