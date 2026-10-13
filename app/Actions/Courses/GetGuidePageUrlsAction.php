<?php

namespace App\Actions\Courses;

use App\Models\Course;
use Illuminate\Support\Collection;

class GetGuidePageUrlsAction
{
    /**
     * @return Collection<int, string>
     */
    public function handle(Course $course): Collection
    {
        return collect($course->guide_pages ?? [])
            ->values()
            ->map(fn (string $path, int $index): string => route('learning.guide-page', [
                'course' => $course,
                'page' => $index + 1,
            ]));
    }
}
