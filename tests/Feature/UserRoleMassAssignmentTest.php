<?php

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use Livewire\Livewire;

test('role is not mass assignable on the User model', function () {
    $user = User::create([
        'name' => 'Nueva Cuenta',
        'email' => 'nueva-cuenta@example.com',
        'password' => 'Passw0rd!',
        'role' => UserRole::Admin,
    ]);

    expect($user->refresh()->role)->toBe(UserRole::Student);
});

test('an admin can still change a user role from the Filament edit page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    Livewire::actingAs($admin);

    Livewire::test(EditUser::class, ['record' => $student->getRouteKey()])
        ->fillForm(['role' => UserRole::Admin->value])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($student->refresh()->role)->toBe(UserRole::Admin);
});
