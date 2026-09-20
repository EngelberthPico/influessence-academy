<?php

namespace App\Actions\Courses;

use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use Illuminate\Support\Collection;

class GetActiveCoursesForUserAction
{
    /**
     * @return Collection<int, Course>
     */
    public function handle(User $user): Collection
    {
        return CourseAccess::query()
            ->where('user_id', $user->id)
            ->active()
            ->with('course')
            ->orderByDesc('granted_at')
            ->get()
            ->pluck('course');
    }
}
