<?php

use App\Enums\CourseType;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\User;

test('learning show for a guide with access displays each page through the protected route and as a thumbnail', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create([
        'type' => CourseType::Guide,
        'guide_pages' => ['guides/sample/page-1.jpg', 'guides/sample/page-2.jpg', 'guides/sample/page-3.jpg'],
    ]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));
    $content = $response->content();

    $response->assertOk();

    foreach ([1, 2, 3] as $page) {
        $url = route('learning.guide-page', [$course, $page]);
        $response->assertSee($url, false);
        // Each protected URL is used twice: once as the thumbnail and once as the main viewer image.
        expect(substr_count($content, $url))->toBe(2);
    }

    expect(substr_count($content, 'data-guide-thumb="'))->toBe(3);
});

test('learning show for a guide with a single page hides the thumbnails and the prev/next controls', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create([
        'type' => CourseType::Guide,
        'guide_pages' => ['guides/sample/page-1.jpg'],
    ]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee(route('learning.guide-page', [$course, 1]), false);
    expect(substr_count($response->content(), 'data-guide-thumb="'))->toBe(0);
    $response->assertDontSee('Anterior');
    $response->assertDontSee('Siguiente');
});

test('learning show for a guide without access returns a 403', function () {
    $user = User::factory()->create();
    $course = Course::factory()->guide()->create();

    $this->actingAs($user)->get(route('learning.show', $course))->assertForbidden();
});

test('learning show for a guide with no pages shows a message instead of broken images', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create([
        'type' => CourseType::Guide,
        'guide_pages' => null,
    ]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('Esta guía todavía no tiene páginas disponibles');
});

test('the video lesson route for a guide returns a 404', function () {
    $user = User::factory()->create();
    $course = Course::factory()->guide()->create();
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $otherCourse = Course::factory()->create();
    $module = CourseModule::factory()->for($otherCourse)->create();
    $lesson = Lesson::factory()->for($module, 'module')->create();

    $this->actingAs($user)
        ->get(route('learning.lesson', [$course, $lesson]))
        ->assertNotFound();
});
