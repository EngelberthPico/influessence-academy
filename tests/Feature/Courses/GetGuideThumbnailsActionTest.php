<?php

use App\Actions\Courses\GetGuideThumbnailsAction;
use App\Enums\CourseType;
use App\Models\Course;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    $this->action = new GetGuideThumbnailsAction;
});

test('returns a small jpeg data uri for a jpg page', function () {
    $path = UploadedFile::fake()->image('page-1.jpg', 400, 600)->storeAs('guides/test', 'page-1.jpg', 'local');
    $course = Course::factory()->create(['type' => CourseType::Guide, 'guide_pages' => [$path]]);

    $thumbnails = $this->action->handle($course);

    expect($thumbnails)->toHaveCount(1);
    expect($thumbnails[0])->toStartWith('data:image/jpeg;base64,');

    $bytes = base64_decode(substr($thumbnails[0], strlen('data:image/jpeg;base64,')));
    expect(strlen($bytes))->toBeLessThan(12 * 1024);

    $thumbnail = imagecreatefromstring($bytes);
    expect($thumbnail)->not->toBeFalse();
    expect(imagesx($thumbnail))->toBe(160);
});

test('returns a small jpeg data uri for a png page', function () {
    $path = UploadedFile::fake()->image('page-1.png', 400, 600)->storeAs('guides/test', 'page-1.png', 'local');
    $course = Course::factory()->create(['type' => CourseType::Guide, 'guide_pages' => [$path]]);

    $thumbnails = $this->action->handle($course);

    expect($thumbnails[0])->toStartWith('data:image/jpeg;base64,');
});

test('reuses the cached thumbnail on a second call', function () {
    $path = UploadedFile::fake()->image('page-1.jpg', 400, 600)->storeAs('guides/test', 'page-1.jpg', 'local');
    $course = Course::factory()->create(['type' => CourseType::Guide, 'guide_pages' => [$path]]);

    $first = $this->action->handle($course)[0];

    $thumbPath = collect(Storage::disk('local')->allFiles('guides/thumbs'))->first();
    expect($thumbPath)->not->toBeNull();

    $cachedBytes = Storage::disk('local')->get($thumbPath);

    $second = $this->action->handle($course)[0];

    expect($second)->toBe($first);
    expect(Storage::disk('local')->get($thumbPath))->toBe($cachedBytes);
});

test('regenerates the thumbnail when the original file is replaced', function () {
    $path = UploadedFile::fake()->image('page-1.jpg', 400, 600)->storeAs('guides/test', 'page-1.jpg', 'local');
    $course = Course::factory()->create(['type' => CourseType::Guide, 'guide_pages' => [$path]]);

    $this->action->handle($course);
    $originalThumbPath = collect(Storage::disk('local')->allFiles('guides/thumbs'))->first();

    $replacement = UploadedFile::fake()->image('page-1.jpg', 200, 300);
    Storage::disk('local')->put($path, file_get_contents($replacement->getRealPath()));
    touch(Storage::disk('local')->path($path), time() + 60);

    $this->action->handle($course);
    $thumbPaths = Storage::disk('local')->allFiles('guides/thumbs');

    expect($thumbPaths)->toHaveCount(2);
    expect($thumbPaths)->toContain($originalThumbPath);
});

test('returns null without throwing for a file gd cannot read', function () {
    $path = 'guides/test/not-an-image.jpg';
    Storage::disk('local')->put($path, 'this is not an image');
    $course = Course::factory()->create(['type' => CourseType::Guide, 'guide_pages' => [$path]]);

    $thumbnails = $this->action->handle($course);

    expect($thumbnails[0])->toBeNull();
});

test('returns null for a page path that does not exist on disk', function () {
    $course = Course::factory()->create(['type' => CourseType::Guide, 'guide_pages' => ['guides/missing.jpg']]);

    $thumbnails = $this->action->handle($course);

    expect($thumbnails[0])->toBeNull();
});
