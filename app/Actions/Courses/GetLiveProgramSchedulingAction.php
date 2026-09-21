<?php

namespace App\Actions\Courses;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Collection;

class GetLiveProgramSchedulingAction
{
    /**
     * @return array{bookings: Collection<int, Booking>, total: ?int, used: int, remaining: ?int}
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

        return [
            'bookings' => $bookings,
            'total' => $total,
            'used' => $used,
            'remaining' => $remaining,
        ];
    }
}
