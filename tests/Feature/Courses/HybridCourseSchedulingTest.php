<?php

use App\Enums\CourseType;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\User;

test('a hybrid course within the redemption window shows the video, the modules and the calendly widget', function () {
    config(['services.calendly.scheduling_url' => 'https://calendly.com/influessenceacademy-info/claseenvivo']);

    $user = User::factory()->create(['email' => 'estudiante@example.com']);
    $course = Course::factory()->create(['type' => CourseType::Hybrid, 'vimeo_id' => '1228428160']);
    $module = CourseModule::factory()->create(['course_id' => $course->id]);
    Lesson::factory()->create(['course_module_id' => $module->id]);

    CourseAccess::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'advisory_redeemable_until' => now()->addDays(3),
    ]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('player.vimeo.com/video/1228428160', false);
    $response->assertSee($module->title);
    $response->assertSee('Agenda tu clase');
    $response->assertSee('id="calendly-embed"', false);
    $response->assertSee('https://assets.calendly.com/assets/external/widget.js', false);
});

test('a hybrid course past the redemption window shows the deadline message instead of the widget', function () {
    config(['services.calendly.scheduling_url' => 'https://calendly.com/influessenceacademy-info/claseenvivo']);

    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Hybrid, 'vimeo_id' => '1228428160']);

    $redeemableUntil = now()->subDay();
    CourseAccess::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'advisory_redeemable_until' => $redeemableUntil,
    ]);

    $response = $this->actingAs($user)->get(route('learning.show', $course));

    $response->assertOk();
    $response->assertSee('player.vimeo.com/video/1228428160', false);
    $response->assertSee('El plazo para agendar tu asesoría venció el '.$redeemableUntil->format('d/m/Y'));
    $response->assertDontSee('id="calendly-embed"', false);
    $response->assertDontSee('https://assets.calendly.com/assets/external/widget.js', false);
});
