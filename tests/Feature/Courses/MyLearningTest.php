<?php

use App\Enums\CourseType;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;

test('a guest is redirected to login from the learning index', function () {
    $this->get(route('learning.index'))->assertRedirect(route('login'));
});

test('a guest is redirected to login from a learning show page', function () {
    $course = Course::factory()->create();

    $this->get(route('learning.show', $course))->assertRedirect(route('login'));
});

test('index lists only the courses with active access for the authenticated user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $accessibleCourse = Course::factory()->create(['title' => 'Curso Accesible']);
    $revokedCourse = Course::factory()->create(['title' => 'Curso Revocado']);
    $otherUsersCourse = Course::factory()->create(['title' => 'Curso De Otra Usuaria']);

    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $accessibleCourse->id]);
    CourseAccess::factory()->revoked()->create(['user_id' => $user->id, 'course_id' => $revokedCourse->id]);
    CourseAccess::factory()->create(['user_id' => $otherUser->id, 'course_id' => $otherUsersCourse->id]);

    $response = $this->actingAs($user)->get(route('learning.index'));

    $response->assertOk();
    $response->assertSee('Curso Accesible');
    $response->assertDontSee('Curso Revocado');
    $response->assertDontSee('Curso De Otra Usuaria');
});

test('index shows an empty state when the user has no active course access', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('learning.index'));

    $response->assertOk();
    $response->assertSee('Todavía no tienes cursos');
});

test('show returns 403 when the user has no access to the course', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    $this->actingAs($user)->get(route('learning.show', $course))->assertForbidden();
});

test('show returns 403 when the access has been revoked', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    CourseAccess::factory()->revoked()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $this->actingAs($user)->get(route('learning.show', $course))->assertForbidden();
});

test('show renders the vimeo player when the course has a valid vimeo id', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['vimeo_id' => '1228428160']);

    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('player.vimeo.com/video/1228428160', false);
});

test('show still works for a course that is not published as long as the user has access', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['vimeo_id' => '1228428160', 'is_published' => false]);

    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('player.vimeo.com/video/1228428160', false);
});

test('show shows a pending message when the course has no vimeo id', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['vimeo_id' => null, 'type' => CourseType::Recorded]);

    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('El video de este curso todavía no está disponible');
    $response->assertDontSee('<iframe', false);
});

test('show shows the live program message when the course type is live program and has no video', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['vimeo_id' => null, 'type' => CourseType::LiveProgram]);

    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('Este programa es en vivo, por eso no tiene un video para ver aquí');
    $response->assertDontSee('<iframe', false);
});

test('an admin sees the link to the admin panel on my account', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('learning.index'));

    $response->assertOk();
    $response->assertSee('Administración');
    $response->assertSee(route('filament.admin.pages.dashboard'), false);
});

test('a regular user does not see the link to the admin panel', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('learning.index'));

    $response->assertOk();
    $response->assertDontSee('Administración');
});

test('the private area no longer shows the starter kit remnants', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('learning.index'));

    $response->assertOk();
    $response->assertDontSee('Repositorio');
    $response->assertDontSee('Repository');
    $response->assertDontSee('Documentación');
    $response->assertDontSee('Documentation');
    $response->assertDontSee('github.com/laravel', false);
    $response->assertDontSee('laravel.com/docs', false);
});
