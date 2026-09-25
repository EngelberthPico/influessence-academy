<?php

use App\Enums\CourseType;
use App\Models\Booking;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;

test('a live program with access shows the calendly widget prefilled with the account email and course id', function () {
    config(['services.calendly.scheduling_url' => 'https://calendly.com/influessenceacademy-info/claseenvivo']);

    $user = User::factory()->create(['email' => 'estudiante@example.com']);
    $course = Course::factory()->create(['type' => CourseType::LiveProgram]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('id="calendly-embed"', false);
    $response->assertSee('https://assets.calendly.com/assets/external/widget.js', false);
    $response->assertSee('estudiante@example.com');
    $response->assertSee('course_id: '.$course->id, false);
});

test('the scheduled classes list only shows bookings for that user and that course', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::LiveProgram, 'session_count' => 3]);
    $otherCourse = Course::factory()->create(['type' => CourseType::LiveProgram]);

    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $otherCourse->id]);
    CourseAccess::factory()->create(['user_id' => $otherUser->id, 'course_id' => $course->id]);

    Booking::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);
    Booking::factory()->create(['user_id' => $user->id, 'course_id' => $otherCourse->id]);
    Booking::factory()->create(['user_id' => $otherUser->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('Has agendado 1 de 3');
});

test('when all sessions are used the widget and calendly script do not render', function () {
    config(['services.calendly.scheduling_url' => 'https://calendly.com/influessenceacademy-info/claseenvivo']);

    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::LiveProgram, 'session_count' => 1]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    Booking::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('Ya agendaste todas las sesiones de tu programa');
    $response->assertDontSee('id="calendly-embed"', false);
    $response->assertDontSee('https://assets.calendly.com/assets/external/widget.js', false);
});

test('when the scheduling url is not configured the unavailable message shows and no script renders', function () {
    config(['services.calendly.scheduling_url' => null]);

    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::LiveProgram]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('El agendamiento no está disponible por ahora');
    $response->assertDontSee('id="calendly-embed"', false);
    $response->assertDontSee('https://assets.calendly.com/assets/external/widget.js', false);
});

test('a recorded course does not show the agenda tu clase section', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Recorded]);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertDontSee('Agenda tu clase');
});
