<?php

namespace App\Http\Controllers;

use App\Actions\Bookings\ConfirmCalendlyBookingAction;
use App\Exceptions\CalendlyConfirmationException;
use App\Http\Requests\ConfirmBookingRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function create(): View
    {
        return view('bookings.create');
    }

    public function confirm(ConfirmBookingRequest $request, ConfirmCalendlyBookingAction $action): JsonResponse
    {
        try {
            $booking = $action->handle(
                user: $request->user(),
                eventUri: $request->string('event_uri')->toString(),
                inviteeUri: $request->string('invitee_uri')->toString(),
            );
        } catch (CalendlyConfirmationException) {
            return response()->json([
                'message' => 'No pudimos confirmar tu reserva. Intenta de nuevo o contáctanos.',
            ], 422);
        }

        return response()->json([
            'message' => 'Reserva confirmada.',
            'booking' => [
                'id' => $booking->id,
                'scheduled_at' => $booking->scheduled_at,
                'status' => $booking->status,
            ],
        ]);
    }
}
