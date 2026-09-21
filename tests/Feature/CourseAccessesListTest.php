<?php

use App\Enums\UserRole;
use App\Filament\Resources\CourseAccesses\Pages\ListCourseAccesses;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    Livewire::actingAs($admin);
});

test('the default grouping for the accesses list is by person', function () {
    $access = CourseAccess::factory()->create();

    $grouping = Livewire::test(ListCourseAccesses::class)->instance()->getTableGrouping();

    expect($grouping)->not->toBeNull();
    expect($grouping->getId())->toBe('user_id');
    expect($grouping->getKey($access->fresh(['user'])))->toBe($access->user_id);
});

test('the person filter shows only that person\'s accesses', function () {
    $target = User::factory()->create();
    $other = User::factory()->create();

    $targetAccess = CourseAccess::factory()->create(['user_id' => $target->id]);
    $otherAccess = CourseAccess::factory()->create(['user_id' => $other->id]);

    Livewire::test(ListCourseAccesses::class)
        ->filterTable('user_id', $target)
        ->assertCanSeeTableRecords([$targetAccess])
        ->assertCanNotSeeTableRecords([$otherAccess]);
});

test('two people with the same name end up in different groups', function () {
    $userA = User::factory()->create(['name' => 'María López', 'email' => 'maria.a@example.com']);
    $userB = User::factory()->create(['name' => 'María López', 'email' => 'maria.b@example.com']);

    $accessA = CourseAccess::factory()->create(['user_id' => $userA->id])->fresh(['user']);
    $accessB = CourseAccess::factory()->create(['user_id' => $userB->id])->fresh(['user']);

    $livewire = Livewire::test(ListCourseAccesses::class);
    $grouping = $livewire->instance()->getTableGrouping();

    expect($grouping->getKey($accessA))->not->toBe($grouping->getKey($accessB));
    expect($grouping->getTitle($accessA))->toBe('María López · maria.a@example.com');
    expect($grouping->getTitle($accessB))->toBe('María López · maria.b@example.com');

    $livewire->assertCanSeeTableRecords([$accessA, $accessB]);
});

test('course is offered as an alternative grouping', function () {
    $course = Course::factory()->create();
    CourseAccess::factory()->create(['course_id' => $course->id]);

    $groups = Livewire::test(ListCourseAccesses::class)->instance()->getTable()->getGroups();

    expect(array_keys($groups))->toBe(['user_id', 'course_id']);
});
