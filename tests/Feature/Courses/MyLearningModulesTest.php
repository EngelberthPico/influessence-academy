<?php

use App\Enums\CourseType;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('show lists modules and lessons ordered by position, skipping modules without lessons', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $moduleB = CourseModule::factory()->for($course)->create(['title' => 'Módulo B', 'position' => 2]);
    $moduleA = CourseModule::factory()->for($course)->create(['title' => 'Módulo A', 'position' => 1]);
    $emptyModule = CourseModule::factory()->for($course)->create(['title' => 'Módulo vacío', 'position' => 3]);

    Lesson::factory()->for($moduleB, 'module')->create(['title' => 'Video B2', 'position' => 2]);
    Lesson::factory()->for($moduleB, 'module')->create(['title' => 'Video B1', 'position' => 1]);
    Lesson::factory()->for($moduleA, 'module')->create(['title' => 'Video A1', 'position' => 1]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSeeInOrder(['Módulo A', 'Video A1', 'Módulo B', 'Video B1', 'Video B2']);
    $response->assertDontSee('Módulo vacío');
});

test('show displays the welcome video and the outline together', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded, 'vimeo_id' => '1228428160']);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $module = CourseModule::factory()->for($course)->create();
    Lesson::factory()->for($module, 'module')->create(['title' => 'Video 1']);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('player.vimeo.com/video/1228428160', false);
    $response->assertSee('Video 1');
    $response->assertSee('Empezar');
});

test('show with modules but no welcome video does not show the unavailable message', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded, 'vimeo_id' => null]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $module = CourseModule::factory()->for($course)->create();
    Lesson::factory()->for($module, 'module')->create();

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertDontSee('El video de este curso todavía no está disponible');
});

test('lesson page shows the player, the title and marks the current video', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $module = CourseModule::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($module, 'module')->create(['title' => 'Mi video', 'vimeo_id' => '1228428160']);

    $response = $this->actingAs($user)->get(route('learning.lesson', [$course, $lesson]));

    $response->assertOk();
    $response->assertSee('player.vimeo.com/video/1228428160', false);
    $response->assertSee('Mi video');
    $response->assertSeeHtmlInOrder(['aria-current="page"', 'Mi video']);
});

test('lesson navigation: first lesson has no previous, last has no next, middle has both', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $module = CourseModule::factory()->for($course)->create();
    $first = Lesson::factory()->for($module, 'module')->create(['title' => 'Primero', 'position' => 1]);
    $middle = Lesson::factory()->for($module, 'module')->create(['title' => 'Medio', 'position' => 2]);
    $last = Lesson::factory()->for($module, 'module')->create(['title' => 'Último', 'position' => 3]);

    $firstResponse = $this->actingAs($user)->get(route('learning.lesson', [$course, $first]));
    $firstResponse->assertOk();
    $firstResponse->assertDontSee('Anterior');
    $firstResponse->assertSee('Siguiente');

    $middleResponse = $this->actingAs($user)->get(route('learning.lesson', [$course, $middle]));
    $middleResponse->assertOk();
    $middleResponse->assertSee('Anterior');
    $middleResponse->assertSee('Siguiente');

    $lastResponse = $this->actingAs($user)->get(route('learning.lesson', [$course, $last]));
    $lastResponse->assertOk();
    $lastResponse->assertSee('Anterior');
    $lastResponse->assertDontSee('Siguiente');
});

test('the lesson after the last one in a module is the first one in the next module', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $moduleA = CourseModule::factory()->for($course)->create(['position' => 1]);
    $moduleB = CourseModule::factory()->for($course)->create(['position' => 2]);

    $lastOfA = Lesson::factory()->for($moduleA, 'module')->create(['title' => 'Último de A', 'position' => 1]);
    $firstOfB = Lesson::factory()->for($moduleB, 'module')->create(['title' => 'Primero de B', 'position' => 1]);

    $response = $this->actingAs($user)->get(route('learning.lesson', [$course, $lastOfA]));

    $response->assertOk();
    $response->assertSee(route('learning.lesson', [$course, $firstOfB]), false);
});

test('a lesson from another course returns 404', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    $otherCourse = Course::factory()->create(['type' => CourseType::Recorded]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $otherCourse->id]);

    $otherModule = CourseModule::factory()->for($otherCourse)->create();
    $otherLesson = Lesson::factory()->for($otherModule, 'module')->create();

    $response = $this->actingAs($user)->get(route('learning.lesson', [$course, $otherLesson]));

    $response->assertNotFound();
});

test('a guest is redirected to login from the lesson page', function () {
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    $module = CourseModule::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($module, 'module')->create();

    $this->get(route('learning.lesson', [$course, $lesson]))->assertRedirect(route('login'));
});

test('lesson returns 403 when the user has no access to the course', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    $module = CourseModule::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($module, 'module')->create();

    $this->actingAs($user)->get(route('learning.lesson', [$course, $lesson]))->assertForbidden();
});

test('lesson returns 403 when the access has been revoked', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    CourseAccess::factory()->revoked()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $module = CourseModule::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($module, 'module')->create();

    $this->actingAs($user)->get(route('learning.lesson', [$course, $lesson]))->assertForbidden();
});

test('lesson works for an unpublished course as long as the access is active', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded, 'is_published' => false]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $module = CourseModule::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($module, 'module')->create();

    $this->actingAs($user)->get(route('learning.lesson', [$course, $lesson]))->assertOk();
});

test('the lesson page query count does not grow as modules and lessons are added', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $module = CourseModule::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($module, 'module')->create();

    DB::enableQueryLog();
    $this->actingAs($user)->get(route('learning.lesson', [$course, $lesson]))->assertOk();
    $baselineQueryCount = count(DB::getQueryLog());
    DB::flushQueryLog();

    foreach (range(1, 3) as $i) {
        $extraModule = CourseModule::factory()->for($course)->create();
        Lesson::factory()->for($extraModule, 'module')->count(3)->create();
    }

    DB::flushQueryLog();
    $this->actingAs($user)->get(route('learning.lesson', [$course, $lesson]))->assertOk();
    $queryCountWithMoreContent = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($queryCountWithMoreContent)->toBe($baselineQueryCount);
});
