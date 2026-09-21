<?php

use App\Enums\UserRole;
use App\Models\User;
use Filament\Auth\Pages\Login;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

test('an admin who opens /admin lands on the accesses list, not a 404', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertRedirect(route('filament.admin.resources.course-accesses.index'));
    $this->followRedirects($response)->assertOk();
});

test('logging into the admin panel redirects to /admin, which itself lands on the accesses list', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin, 'password' => bcrypt('password')]);

    Livewire::test(Login::class)
        ->fillForm([
            'email' => $admin->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertRedirect('/admin');

    $response = $this->get('/admin');
    $response->assertRedirect(route('filament.admin.resources.course-accesses.index'));
    $this->followRedirects($response)->assertOk()->assertSee('Accesos');
});

test('the dashboard route no longer exists', function () {
    expect(Route::has('filament.admin.pages.dashboard'))->toBeFalse();
});

test('accesses is the first item in the admin navigation menu', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get('/admin/course-accesses');

    $response->assertOk();
    $response->assertSeeInOrder(['Accesos', 'Cursos', 'Compras', 'Usuarios']);
});

test('a non-admin cannot open the admin panel root either', function () {
    $user = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($user)->get('/admin')->assertForbidden();
});
