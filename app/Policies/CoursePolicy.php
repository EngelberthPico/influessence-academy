<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view the course's content (lessons, video, guide pages).
     *
     * Named "access" instead of "view" on purpose: Filament's admin resources
     * automatically enforce any policy method that matches their standard CRUD
     * actions (view, update, delete, ...). This rule is about whether a student
     * purchased the course, which has nothing to do with the admin panel, so it
     * uses a name Filament never checks.
     */
    public function access(User $user, Course $course): bool
    {
        return $user->hasAccessTo($course);
    }
}
