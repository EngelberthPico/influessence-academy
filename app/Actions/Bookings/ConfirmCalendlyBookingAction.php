<?php

namespace App\Actions\Bookings;

use App\Actions\Courses\GetLiveProgramSchedulingAction;
use App\Enums\BookingStatus;
use App\Enums\CourseType;
use App\Exceptions\CalendlyConfirmationException;
use App\Exceptions\CourseBookingException;
use App\Models\Booking;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ConfirmCalendlyBookingAction
{
    public function __construct(private readonly GetLiveProgramSchedulingAction $schedulingAction) {}

    public function handle(User $user, string $eventUri, string $inviteeUri, ?Course $course = null): Booking
    {
        if ($course) {
            if (! $user->hasAccessTo($course)) {
                throw new CourseBookingException('No tienes acceso a este curso.', 403);
            }

            if ($course->type !== CourseType::LiveProgram) {
                throw new CourseBookingException('Este curso no incluye clases en vivo', 422);
            }

            $existingBooking = Booking::query()
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('calendly_event_uri', $eventUri)
                ->first();

            if ($existingBooking) {
                return $existingBooking;
            }

            $scheduling = $this->schedulingAction->handle($user, $course);

            if ($scheduling['remaining'] !== null && $scheduling['remaining'] <= 0) {
                throw new CourseBookingException('Ya agendaste todas las sesiones de este programa. Escríbenos si necesitas cambiar una', 422);
            }
        }

        try {
            $event = Http::withToken(config('services.calendly.token'))
                ->connectTimeout(3)
                ->timeout(10)
                ->get($eventUri)
                ->throw()
                ->json('resource');

            $invitee = Http::withToken(config('services.calendly.token'))
                ->connectTimeout(3)
                ->timeout(10)
                ->get($inviteeUri)
                ->throw()
                ->json('resource');
        } catch (Throwable $exception) {
            Log::error('No se pudo confirmar la reserva con la API de Calendly.', [
                'user_id' => $user->id,
                'event_uri' => $eventUri,
                'invitee_uri' => $inviteeUri,
                'exception' => $exception->getMessage(),
            ]);

            throw new CalendlyConfirmationException('No pudimos confirmar la reserva con Calendly.', previous: $exception);
        }

        $startTime = $event['start_time'] ?? null;
        $inviteeEmail = $invitee['email'] ?? null;

        if (blank($startTime) || blank($inviteeEmail)) {
            Log::error('La respuesta de Calendly no incluyó los datos esperados.', [
                'user_id' => $user->id,
                'event_uri' => $eventUri,
                'invitee_uri' => $inviteeUri,
            ]);

            throw new CalendlyConfirmationException('No pudimos confirmar la reserva con Calendly.');
        }

        if (Str::lower(trim($inviteeEmail)) !== Str::lower($user->email)) {
            Log::error('El correo del invitado de Calendly no coincide con el del usuario autenticado.', [
                'user_id' => $user->id,
                'event_uri' => $eventUri,
            ]);

            throw new CalendlyConfirmationException('El correo del invitado no coincide con tu cuenta.');
        }

        return Booking::firstOrCreate(
            ['calendly_event_uri' => $eventUri],
            [
                'user_id' => $user->id,
                'course_id' => $course?->id,
                'scheduled_at' => $startTime,
                'status' => BookingStatus::Scheduled,
            ],
        );
    }
}
