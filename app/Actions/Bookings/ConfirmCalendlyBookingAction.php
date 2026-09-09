<?php

namespace App\Actions\Bookings;

use App\Enums\BookingStatus;
use App\Exceptions\CalendlyConfirmationException;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ConfirmCalendlyBookingAction
{
    public function handle(User $user, string $eventUri, string $inviteeUri): Booking
    {
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
                'scheduled_at' => $startTime,
                'status' => BookingStatus::Scheduled,
            ],
        );
    }
}
