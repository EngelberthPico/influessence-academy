<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('guests cannot confirm a booking', function () {
    $response = $this->postJson('/bookings/confirmar', [
        'event_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA',
        'invitee_uri' => 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB',
    ]);

    $response->assertUnauthorized();
});

test('a confirmed booking is created with the data returned by the calendly api', function () {
    $user = User::factory()->create();

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
    ]);

    $response->assertOk();

    expect(Booking::count())->toBe(1);

    $booking = Booking::first();

    expect($booking->user_id)->toBe($user->id)
        ->and($booking->calendly_event_uri)->toBe($eventUri)
        ->and($booking->status)->toBe(BookingStatus::Scheduled)
        ->and($booking->scheduled_at->toIso8601String())->toBe('2026-10-01T15:00:00+00:00');
});

test('confirming the same calendly event twice does not duplicate the booking', function () {
    $user = User::factory()->create();

    $eventUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA';
    $inviteeUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB';

    Http::preventStrayRequests();

    Http::fake([
        $eventUri => Http::response(['resource' => ['uri' => $eventUri, 'start_time' => '2026-10-01T15:00:00Z']]),
        $inviteeUri => Http::response(['resource' => ['uri' => $inviteeUri, 'email' => $user->email]]),
    ]);

    $payload = ['event_uri' => $eventUri, 'invitee_uri' => $inviteeUri];

    $this->actingAs($user)->postJson('/bookings/confirmar', $payload)->assertOk();
    $this->actingAs($user)->postJson('/bookings/confirmar', $payload)->assertOk();

    expect(Booking::count())->toBe(1);
});

test('a booking is not created when the invitee email does not match the authenticated user', function () {
    $user = User::factory()->create(['email' => 'user@example.com']);

    $eventUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA';
    $inviteeUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB';

    Http::preventStrayRequests();

    Http::fake([
        $eventUri => Http::response(['resource' => ['uri' => $eventUri, 'start_time' => '2026-10-01T15:00:00Z']]),
        $inviteeUri => Http::response(['resource' => ['uri' => $inviteeUri, 'email' => 'someone-else@example.com']]),
    ]);

    $response = $this->actingAs($user)->postJson('/bookings/confirmar', [
        'event_uri' => $eventUri,
        'invitee_uri' => $inviteeUri,
    ]);

    $response->assertStatus(422);

    expect(Booking::count())->toBe(0);
});

test('a booking is not created when the calendly api call fails', function () {
    $user = User::factory()->create();

    $eventUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA';
    $inviteeUri = 'https://api.calendly.com/scheduled_events/AAAAAAAAAAAAAAAA/invitees/BBBBBBBBBBBBBBBB';

    Http::preventStrayRequests();

    Http::fake([
        $eventUri => Http::response(null, 500),
        $inviteeUri => Http::response(['resource' => ['email' => $user->email]]),
    ]);

    $response = $this->actingAs($user)->postJson('/bookings/confirmar', [
        'event_uri' => $eventUri,
        'invitee_uri' => $inviteeUri,
    ]);

    $response->assertStatus(422);

    expect(Booking::count())->toBe(0);
});

test('the event and invitee uris must belong to the calendly api', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/bookings/confirmar', [
        'event_uri' => 'https://evil.example.com/steal-token',
        'invitee_uri' => 'https://evil.example.com/steal-token',
    ]);

    $response->assertInvalid(['event_uri', 'invitee_uri']);
});
