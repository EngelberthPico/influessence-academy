<?php

use App\Enums\CourseType;
use App\Enums\UserRole;
use App\Filament\Resources\Courses\Pages\CreateCourse;
use App\Models\User;
use Livewire\Livewire;

test('creating a course from filament fails validation with an invalid vimeo id', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Livewire::actingAs($admin);

    Livewire::test(CreateCourse::class)
        ->fillForm([
            'title' => 'Curso de prueba',
            'price_cents' => 100,
            'type' => CourseType::Recorded->value,
            'vimeo_id' => 'esto no es un link de vimeo',
        ])
        ->call('create')
        ->assertHasFormErrors(['vimeo_id']);
});

test('creating a course from filament passes validation with a valid vimeo id', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Livewire::actingAs($admin);

    Livewire::test(CreateCourse::class)
        ->fillForm([
            'title' => 'Curso de prueba',
            'price_cents' => 100,
            'type' => CourseType::Recorded->value,
            'vimeo_id' => 'https://vimeo.com/1228428160',
        ])
        ->call('create')
        ->assertHasNoFormErrors(['vimeo_id']);
});
