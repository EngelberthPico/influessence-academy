<?php

use App\Actions\Courses\GetGuidePageUrlsAction;
use App\Enums\CourseType;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Js;

test('learning show for a guide with access renders a single page image and an embedded thumbnail per page', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $paths = collect([1, 2, 3])->map(
        fn (int $page) => UploadedFile::fake()->image("page-{$page}.jpg", 400, 600)->storeAs('guides/test', "page-{$page}.jpg", 'local')
    );
    $course = Course::factory()->create([
        'type' => CourseType::Guide,
        'guide_pages' => $paths->all(),
    ]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));
    $content = $response->content();

    $response->assertOk();

    // The page image is loaded through a single <img>, driven by Alpine, not one per page.
    expect(substr_count($content, '@load="handleImageLoad()"'))->toBe(1);
    expect(substr_count($content, 'x-on:error="handleImageError()"'))->toBe(1);

    // The protected routes for every page still travel to the browser, inside the component's data.
    $expectedUrls = (string) Js::from(app(GetGuidePageUrlsAction::class)->handle($course->fresh()));
    $response->assertSee($expectedUrls, false);

    expect(substr_count($content, 'data-guide-thumb="'))->toBe(3);
    expect(substr_count($content, 'data:image/jpeg;base64,'))->toBe(3);
});

test('learning show for a guide with several pages renders the prev/next controls before the page image', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create([
        'type' => CourseType::Guide,
        'guide_pages' => ['guides/sample/page-1.jpg', 'guides/sample/page-2.jpg', 'guides/sample/page-3.jpg'],
    ]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));
    $content = $response->content();

    $response->assertOk();

    $controlsPosition = strpos($content, 'Anterior');
    $pageImagePosition = strpos($content, '@load="handleImageLoad()"');

    expect($controlsPosition)->not->toBeFalse();
    expect($pageImagePosition)->not->toBeFalse();
    expect($controlsPosition)->toBeLessThan($pageImagePosition);
});

test('learning show renders touch swipe handlers and touch-action classes on the page viewer', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create([
        'type' => CourseType::Guide,
        'guide_pages' => ['guides/sample/page-1.jpg', 'guides/sample/page-2.jpg'],
    ]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('@touchstart.passive="handleTouchStart($event)"', false);
    $response->assertSee('@touchend.passive="handleTouchEnd($event)"', false);
    $response->assertSee('touch-pan-y', false);
    $response->assertSee('touch-pinch-zoom', false);
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
    $response->assertSee('@load="handleImageLoad()"', false);
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
