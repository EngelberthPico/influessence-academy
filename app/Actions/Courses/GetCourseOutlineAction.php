<?php

namespace App\Actions\Courses;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use Illuminate\Support\Collection;

class GetCourseOutlineAction
{
    /**
     * @return Collection<int, CourseModule>
     */
    public function handle(Course $course): Collection
    {
        return $course->modules()
            ->with('lessons')
            ->get()
            ->filter(fn (CourseModule $module): bool => $module->lessons->isNotEmpty())
            ->values();
    }

    /**
     * Finds the lesson immediately before and after the given one, in course
     * order, without querying the database again.
     *
     * @param  Collection<int, CourseModule>  $outline
     * @return array{0: ?Lesson, 1: ?Lesson}
     */
    public function findAdjacentLessons(Collection $outline, Lesson $current): array
    {
        $flatLessons = $outline->flatMap(fn (CourseModule $module) => $module->lessons)->values();

        $index = $flatLessons->search(fn (Lesson $lesson): bool => $lesson->is($current));

        if ($index === false) {
            return [null, null];
        }

        return [
            $flatLessons->get($index - 1),
            $flatLessons->get($index + 1),
        ];
    }
}
