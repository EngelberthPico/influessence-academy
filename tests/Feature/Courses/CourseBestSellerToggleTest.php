<?php

use App\Enums\CourseType;
use App\Enums\UserRole;
use App\Filament\Resources\Courses\Pages\CreateCourse;
use App\Filament\Resources\Courses\Pages\EditCourse;
use App\Filament\Resources\Courses\Pages\ListCourses;
use App\Models\Course;
use App\Models\User;
use Livewire\Livewire;

test('an admin can mark a course as best seller when creating it', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Livewire::actingAs($admin);

    Livewire::test(CreateCourse::class)
        ->fillForm([
            'title' => 'Curso de prueba',
            'price_cents' => 100,
            'type' => CourseType::LiveProgram->value,
            'session_count' => 4,
            'is_best_seller' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Course::where('title', 'Curso de prueba')->firstOrFail()->is_best_seller)->toBeTrue();
});

test('an admin can toggle a course as best seller when editing it', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $course = Course::factory()->create(['is_best_seller' => false]);

    Livewire::actingAs($admin);

    Livewire::test(EditCourse::class, ['record' => $course->getRouteKey()])
        ->fillForm(['is_best_seller' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($course->refresh()->is_best_seller)->toBeTrue();
});

test('the courses list shows the best seller column value', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $bestSeller = Course::factory()->bestSeller()->create();
    $regular = Course::factory()->create(['is_best_seller' => false]);

    Livewire::actingAs($admin);

    Livewire::test(ListCourses::class)
        ->assertCanSeeTableRecords([$bestSeller, $regular])
        ->assertTableColumnStateSet('is_best_seller', true, record: $bestSeller)
        ->assertTableColumnStateSet('is_best_seller', false, record: $regular);
});
