<?php

use App\Enums\CourseType;
use App\Enums\UserRole;
use App\Filament\Resources\Courses\Pages\CreateCourse;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('creating a guide from filament with images saves guide_pages as an ordered list', function () {
    Storage::fake('local');

    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Livewire::actingAs($admin);

    Livewire::test(CreateCourse::class)
        ->fillForm([
            'title' => 'Guía de prueba',
            'price_cents' => 10,
            'type' => CourseType::Guide->value,
            'guide_pages' => [
                UploadedFile::fake()->image('page-1.jpg'),
                UploadedFile::fake()->image('page-2.jpg'),
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $course = Course::where('title', 'Guía de prueba')->first();

    expect($course)->not->toBeNull()
        ->and($course->guide_pages)->toBeArray()
        ->and($course->guide_pages)->toHaveCount(2);
});

test('publishing a guide from filament without any pages fails validation', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Livewire::actingAs($admin);

    Livewire::test(CreateCourse::class)
        ->fillForm([
            'title' => 'Guía sin páginas',
            'price_cents' => 10,
            'type' => CourseType::Guide->value,
            'is_published' => true,
        ])
        ->call('create')
        ->assertHasFormErrors(['guide_pages']);
});
