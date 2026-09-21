<?php

use App\Enums\CourseType;
use App\Enums\UserRole;
use App\Filament\Resources\Courses\Pages\EditCourse;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    Livewire::actingAs($this->admin);
});

test('an admin can add modules with videos and they save in order', function () {
    $course = Course::factory()->create(['type' => CourseType::Recorded]);

    Livewire::test(EditCourse::class, ['record' => $course->getRouteKey()])
        ->fillForm([
            'modules' => [
                'module-1' => [
                    'title' => 'Módulo 1',
                    'description' => null,
                    'lessons' => [
                        'lesson-1' => ['title' => 'Video 1', 'vimeo_id' => '1228428160', 'description' => null],
                        'lesson-2' => ['title' => 'Video 2', 'vimeo_id' => '1228428161', 'description' => null],
                    ],
                ],
                'module-2' => [
                    'title' => 'Módulo 2',
                    'description' => null,
                    'lessons' => [
                        'lesson-3' => ['title' => 'Video 3', 'vimeo_id' => '1228428162', 'description' => null],
                        'lesson-4' => ['title' => 'Video 4', 'vimeo_id' => '1228428163', 'description' => null],
                    ],
                ],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $course->refresh();

    expect($course->modules)->toHaveCount(2);
    expect($course->modules->pluck('title')->all())->toBe(['Módulo 1', 'Módulo 2']);
    expect($course->modules->pluck('position')->all())->toBe([1, 2]);

    $firstModule = $course->modules->firstWhere('title', 'Módulo 1');
    expect($firstModule->lessons)->toHaveCount(2);
    expect($firstModule->lessons->pluck('title')->all())->toBe(['Video 1', 'Video 2']);
    expect($firstModule->lessons->pluck('position')->all())->toBe([1, 2]);
});

test('reordering modules and lessons updates their position', function () {
    $course = Course::factory()->create(['type' => CourseType::Recorded]);

    $moduleA = CourseModule::factory()->for($course)->create(['title' => 'Módulo A', 'position' => 1]);
    $moduleB = CourseModule::factory()->for($course)->create(['title' => 'Módulo B', 'position' => 2]);

    $lessonA1 = Lesson::factory()->for($moduleA, 'module')->create(['title' => 'Video A1', 'position' => 1]);
    $lessonA2 = Lesson::factory()->for($moduleA, 'module')->create(['title' => 'Video A2', 'position' => 2]);

    // Reorder: module B first, and inside module A swap its two lessons.
    // Uses ->set() instead of ->fillForm() on purpose: fillForm() merges by
    // dot-path and only prunes numeric/list keys (see
    // InteractsWithSchemas::unsetMissingNumericArrayKeys), so it can't
    // reorder or drop "record-{id}" keyed items the way a real drag-and-drop
    // or delete button does. ->set() replaces the whole array, matching what
    // actually reaches the server once Livewire syncs the reordered state.
    Livewire::test(EditCourse::class, ['record' => $course->getRouteKey()])
        ->set('data.modules', [
            "record-{$moduleB->id}" => [
                'title' => $moduleB->title,
                'description' => $moduleB->description,
                'lessons' => [],
            ],
            "record-{$moduleA->id}" => [
                'title' => $moduleA->title,
                'description' => $moduleA->description,
                'lessons' => [
                    "record-{$lessonA2->id}" => ['title' => $lessonA2->title, 'vimeo_id' => $lessonA2->vimeo_id, 'description' => null],
                    "record-{$lessonA1->id}" => ['title' => $lessonA1->title, 'vimeo_id' => $lessonA1->vimeo_id, 'description' => null],
                ],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($moduleB->refresh()->position)->toBe(1);
    expect($moduleA->refresh()->position)->toBe(2);
    expect($lessonA2->refresh()->position)->toBe(1);
    expect($lessonA1->refresh()->position)->toBe(2);
});

test('removing a video from the form deletes it', function () {
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    $module = CourseModule::factory()->for($course)->create();
    $keep = Lesson::factory()->for($module, 'module')->create(['position' => 1]);
    $remove = Lesson::factory()->for($module, 'module')->create(['position' => 2]);

    // ->set() is used here too, for the same reason as the reorder test above.
    Livewire::test(EditCourse::class, ['record' => $course->getRouteKey()])
        ->set('data.modules', [
            "record-{$module->id}" => [
                'title' => $module->title,
                'description' => $module->description,
                'lessons' => [
                    "record-{$keep->id}" => ['title' => $keep->title, 'vimeo_id' => $keep->vimeo_id, 'description' => null],
                ],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Lesson::find($keep->id))->not->toBeNull();
    expect(Lesson::find($remove->id))->toBeNull();
});

test('saving the course without touching the content section keeps the same module and lesson ids', function () {
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    $module = CourseModule::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($module, 'module')->create();

    Livewire::test(EditCourse::class, ['record' => $course->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(CourseModule::find($module->id))->not->toBeNull();
    expect(Lesson::find($lesson->id))->not->toBeNull();
    expect($course->modules()->count())->toBe(1);
    expect($module->lessons()->count())->toBe(1);
});

test('an invalid vimeo id on a video fails validation', function () {
    $course = Course::factory()->create(['type' => CourseType::Recorded]);

    Livewire::test(EditCourse::class, ['record' => $course->getRouteKey()])
        ->fillForm([
            'modules' => [
                'module-1' => [
                    'title' => 'Módulo 1',
                    'description' => null,
                    'lessons' => [
                        'lesson-1' => ['title' => 'Video 1', 'vimeo_id' => 'esto no es un link de vimeo', 'description' => null],
                    ],
                ],
            ],
        ])
        ->call('save')
        ->assertHasFormErrors();

    expect(CourseModule::query()->count())->toBe(0);
});

test('saving a live program course does not delete its existing modules or lessons', function () {
    $course = Course::factory()->create(['type' => CourseType::LiveProgram]);
    $module = CourseModule::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($module, 'module')->create();

    Livewire::test(EditCourse::class, ['record' => $course->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(CourseModule::find($module->id))->not->toBeNull();
    expect(Lesson::find($lesson->id))->not->toBeNull();
});
