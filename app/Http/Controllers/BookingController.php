<?php

namespace App\Http\Controllers;

use App\Actions\Bookings\ConfirmCalendlyBookingAction;
use App\Actions\Courses\GetCourseSchedulingAction;
use App\Exceptions\CalendlyConfirmationException;
use App\Exceptions\CourseBookingException;
use App\Http\Requests\ConfirmBookingRequest;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function create(): View
    {
        return view('bookings.create');
    }

    public function confirm(ConfirmBookingRequest $request, ConfirmCalendlyBookingAction $action, GetCourseSchedulingAction $schedulingAction): JsonResponse
    {
        $course = $request->integer('course_id') ? Course::find($request->integer('course_id')) : null;

        try {
            $booking = $action->handle(
                user: $request->user(),
                eventUri: $request->string('event_uri')->toString(),
                inviteeUri: $request->string('invitee_uri')->toString(),
                course: $course,
            );
        } catch (CourseBookingException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->status());
        } catch (CalendlyConfirmationException) {
            return response()->json([
                'message' => 'No pudimos confirmar tu reserva. Intenta de nuevo o contáctanos.',
            ], 422);
        }

        $data = [
            'message' => 'Reserva confirmada.',
            'booking' => [
                'id' => $booking->id,
                'scheduled_at' => $booking->scheduled_at,
                'status' => $booking->status,
            ],
        ];

        if ($course) {
            $data['remaining_sessions'] = $schedulingAction->handle($request->user(), $course)['remaining'];
        }

        return response()->json($data);
    }
}
