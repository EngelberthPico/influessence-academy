<?php

namespace App\Actions\Courses;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GetCourseSchedulingAction
{
    /**
     * @return array{bookings: Collection<int, Booking>, total: ?int, used: int, remaining: ?int, redeemableUntil: ?Carbon, deadlinePassed: bool}
     */
    public function handle(User $user, Course $course): array
    {
        $bookings = Booking::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', '!=', BookingStatus::Canceled)
            ->orderBy('scheduled_at')
            ->get();

        $total = $course->session_count;
        $used = $bookings->count();
        $remaining = $total === null ? null : max(0, $total - $used);

        $courseAccess = CourseAccess::query()
            ->active()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $redeemableUntil = $courseAccess?->advisory_redeemable_until;
        $deadlinePassed = $redeemableUntil !== null && $redeemableUntil->isPast();

        return [
            'bookings' => $bookings,
            'total' => $total,
            'used' => $used,
            'remaining' => $remaining,
            'redeemableUntil' => $redeemableUntil,
            'deadlinePassed' => $deadlinePassed,
        ];
    }
}
