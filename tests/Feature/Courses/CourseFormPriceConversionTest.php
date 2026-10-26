<?php

use App\Enums\CourseType;
use App\Enums\UserRole;
use App\Filament\Resources\Courses\Pages\CreateCourse;
use App\Models\Course;
use App\Models\User;
use Livewire\Livewire;

test('creating a course from filament with a dollar amount stores it in cents', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Livewire::actingAs($admin);

    Livewire::test(CreateCourse::class)
        ->fillForm([
            'title' => 'Curso con centavos',
            'price_cents' => 69.99,
            'type' => CourseType::Recorded->value,
            'modules' => [],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Course::where('title', 'Curso con centavos')->first()->price_cents)->toBe(6999);
});
