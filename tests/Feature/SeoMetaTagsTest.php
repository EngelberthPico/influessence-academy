<?php

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;

test('the home page has its own title, description and open graph tags', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('<title>Convierte tu contenido en tu profesión - Influessence Academy</title>', false);
    $response->assertSee('<meta name="description" content="Construye una presencia digital auténtica', false);
    $response->assertSee('<meta property="og:title" content="Convierte tu contenido en tu profesión - Influessence Academy">', false);
    $response->assertDontSee('name="robots"', false);
});

test('the courses index page has its own title and description', function () {
    $response = $this->get(route('courses.index'));

    $response->assertOk();
    $response->assertSee('<title>Elige tu nivel de acompañamiento - Influessence Academy</title>', false);
    $response->assertSee('Elige el acompañamiento que mejor encaje con tus metas, ritmo y nivel', false);
});

test('a course detail page uses the course title and description in its meta tags', function () {
    $course = Course::factory()->create([
        'title' => 'Curso de Reels desde Cero',
        'description' => '<p>Aprende a grabar y editar reels que conectan con tu audiencia.</p>',
        'is_published' => true,
    ]);

    $response = $this->get(route('courses.show', $course));

    $response->assertOk();
    $response->assertSee("<title>{$course->title} - Influessence Academy</title>", false);
    $response->assertSee('Aprende a grabar y editar reels que conectan con tu audiencia.', false);
});

test('legal pages have their own title', function () {
    $response = $this->get(route('legal.privacy'));

    $response->assertOk();
    $response->assertSee('<title>Política de Privacidad - Influessence Academy</title>', false);
});

test('a private account page carries a noindex tag', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.index'));

    $response->assertOk();
    $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);
});

test('the admin panel carries a noindex tag', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get('/admin/course-accesses');

    $response->assertOk();
    $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);
});

test('the google tag manager snippet does not render without a container id', function () {
    config(['services.google_tag_manager.container_id' => null]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('googletagmanager.com', false);
});

test('the google tag manager snippet renders when a container id is configured', function () {
    config(['services.google_tag_manager.container_id' => 'GTM-TEST123']);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('googletagmanager.com', false);
    $response->assertSee("'GTM-TEST123'", false);
});
