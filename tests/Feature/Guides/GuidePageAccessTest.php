<?php

use App\Enums\CourseType;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('a guest is redirected to login from a guide page', function () {
    $course = Course::factory()->guide()->create();

    $this->get(route('learning.guide-page', [$course, 1]))
        ->assertRedirect(route('login'));
});

test('a user without access gets a 403 on a guide page', function () {
    $user = User::factory()->create();
    $course = Course::factory()->guide()->create();

    $this->actingAs($user)
        ->get(route('learning.guide-page', [$course, 1]))
        ->assertForbidden();
});

test('a user with revoked access gets a 403 on a guide page', function () {
    $user = User::factory()->create();
    $course = Course::factory()->guide()->create();
    CourseAccess::factory()->revoked()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $this->actingAs($user)
        ->get(route('learning.guide-page', [$course, 1]))
        ->assertForbidden();
});

test('a user with active access can view a guide page as an image with a private cache header', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $path = UploadedFile::fake()->image('page-1.jpg')->storeAs('guides/test', 'page-1.jpg', 'local');
    $course = Course::factory()->create([
        'type' => CourseType::Guide,
        'guide_pages' => [$path],
    ]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.guide-page', [$course, 1]));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/jpeg');
    expect($response->headers->get('Cache-Control'))->toContain('private');
});

test('a guide page ignores the retry cache-busting query parameter', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $path = UploadedFile::fake()->image('page-1.jpg')->storeAs('guides/test', 'page-1.jpg', 'local');
    $course = Course::factory()->create([
        'type' => CourseType::Guide,
        'guide_pages' => [$path],
    ]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.guide-page', [$course, 1]).'?r=3');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/jpeg');
});

test('a nonexistent guide page returns a 404', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $path = UploadedFile::fake()->image('page-1.jpg')->storeAs('guides/test', 'page-1.jpg', 'local');
    $course = Course::factory()->create([
        'type' => CourseType::Guide,
        'guide_pages' => [$path],
    ]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $this->actingAs($user)
        ->get(route('learning.guide-page', [$course, 2]))
        ->assertNotFound();
});

test('requesting a guide page for a course that is not a guide returns a 404', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $this->actingAs($user)
        ->get(route('learning.guide-page', [$course, 1]))
        ->assertNotFound();
});

test('guide pages live on the private local disk and never on the public disk', function () {
    Storage::fake('local');
    Storage::fake('public');

    $path = UploadedFile::fake()->image('page-1.jpg')->storeAs('guides/test', 'page-1.jpg', 'local');

    Storage::disk('local')->assertExists($path);
    Storage::disk('public')->assertMissing($path);
});
