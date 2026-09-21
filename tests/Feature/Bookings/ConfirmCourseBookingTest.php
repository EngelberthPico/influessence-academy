<?php

use App\Enums\BookingStatus;
use App\Enums\CourseType;
use App\Models\Booking;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('a booking linked to a course is created when the user has access and the course is a live program', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::LiveProgram]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $eventUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA';
    $inviteeUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB';

    Http::preventStrayRequests();
    Http::fake([
        $eventUri => Http::response(['resource' => ['uri' => $eventUri, 'start_time' => '2026-10-01T15:00:00Z']]),
        $inviteeUri => Http::response(['resource' => ['uri' => $inviteeUri, 'email' => $user->email]]),
    ]);

    $response = $this->actingAs($user)->postJson('/bookings/confirmar', [
        'event_uri' => $eventUri,
        'invitee_uri' => $inviteeUri,
        'course_id' => $course->id,
    ]);

    $response->assertOk();
    $response->assertJsonPath('remaining_sessions', null);

    expect(Booking::first()->course_id)->toBe($course->id);
});

test('confirming a booking without access to the course fails with 403 and makes no calendly calls', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::LiveProgram]);

    Http::preventStrayRequests();
    Http::fake();

    $response = $this->actingAs($user)->postJson('/bookings/confirmar', [
        'event_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA',
        'invitee_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB',
        'course_id' => $course->id,
    ]);

    $response->assertForbidden();
    expect(Booking::count())->toBe(0);
});

test('confirming a booking with revoked access to the course fails with 403', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::LiveProgram]);
    CourseAccess::factory()->revoked()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    Http::preventStrayRequests();
    Http::fake();

    $response = $this->actingAs($user)->postJson('/bookings/confirmar', [
        'event_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA',
        'invitee_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB',
        'course_id' => $course->id,
    ]);

    $response->assertForbidden();
    expect(Booking::count())->toBe(0);
});

test('confirming a booking for a course that is not a live program fails with 422', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    Http::preventStrayRequests();
    Http::fake();

    $response = $this->actingAs($user)->postJson('/bookings/confirmar', [
        'event_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA',
        'invitee_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB',
        'course_id' => $course->id,
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('message', 'Este curso no incluye clases en vivo');
    expect(Booking::count())->toBe(0);
});

test('confirming a booking fails with 422 when all sessions are already used and makes no calendly calls', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::LiveProgram, 'session_count' => 2]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    Booking::factory()->count(2)->create(['user_id' => $user->id, 'course_id' => $course->id]);

    Http::preventStrayRequests();
    Http::fake();

    $response = $this->actingAs($user)->postJson('/bookings/confirmar', [
        'event_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA',
        'invitee_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB',
        'course_id' => $course->id,
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('message', 'Ya agendaste todas las sesiones de este programa. Escríbenos si necesitas cambiar una');
    expect(Booking::count())->toBe(2);
});

test('resending the same event uri when sessions are already used returns the existing booking without duplicating it', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::LiveProgram, 'session_count' => 1]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $eventUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA';
    $existing = Booking::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'calendly_event_uri' => $eventUri,
    ]);

    Http::preventStrayRequests();
    Http::fake();

    $response = $this->actingAs($user)->postJson('/bookings/confirmar', [
        'event_uri' => $eventUri,
        'invitee_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB',
        'course_id' => $course->id,
    ]);

    $response->assertOk();
    $response->assertJsonPath('booking.id', $existing->id);
    expect(Booking::count())->toBe(1);
});

test('canceled bookings do not count toward the session limit', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::LiveProgram, 'session_count' => 1]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    Booking::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'status' => BookingStatus::Canceled,
    ]);

    $eventUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA';
    $inviteeUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB';

    Http::preventStrayRequests();
    Http::fake([
        $eventUri => Http::response(['resource' => ['uri' => $eventUri, 'start_time' => '2026-10-01T15:00:00Z']]),
        $inviteeUri => Http::response(['resource' => ['uri' => $inviteeUri, 'email' => $user->email]]),
    ]);

    $response = $this->actingAs($user)->postJson('/bookings/confirmar', [
        'event_uri' => $eventUri,
        'invitee_uri' => $inviteeUri,
        'course_id' => $course->id,
    ]);

    $response->assertOk();
    expect(Booking::where('status', '!=', BookingStatus::Canceled)->count())->toBe(1);
});

test('a course with no session limit never blocks scheduling', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::LiveProgram, 'session_count' => null]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    Booking::factory()->count(5)->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $eventUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA';
    $inviteeUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB';

    Http::preventStrayRequests();
    Http::fake([
        $eventUri => Http::response(['resource' => ['uri' => $eventUri, 'start_time' => '2026-10-01T15:00:00Z']]),
        $inviteeUri => Http::response(['resource' => ['uri' => $inviteeUri, 'email' => $user->email]]),
    ]);

    $response = $this->actingAs($user)->postJson('/bookings/confirmar', [
        'event_uri' => $eventUri,
        'invitee_uri' => $inviteeUri,
        'course_id' => $course->id,
    ]);

    $response->assertOk();
    $response->assertJsonPath('remaining_sessions', null);
});

test('confirming a booking with a nonexistent course id fails validation', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/bookings/confirmar', [
        'event_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA',
        'invitee_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB',
        'course_id' => 999999,
    ]);

    $response->assertInvalid(['course_id']);
});
