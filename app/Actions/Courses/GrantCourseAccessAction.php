<?php

namespace App\Actions\Courses;

use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\Order;
use App\Models\User;

class GrantCourseAccessAction
{
    public function handle(User $user, Course $course, ?Order $order = null): CourseAccess
    {
        return CourseAccess::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'order_id' => $order?->id,
            'granted_at' => now(),
            'advisory_redeemable_until' => $course->redemption_window_days !== null
                ? now()->addDays($course->redemption_window_days)
                : null,
        ]);
    }
}
