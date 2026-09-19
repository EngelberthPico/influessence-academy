<?php

namespace App\Actions\Courses;

use App\Enums\CourseType;
use App\Models\Course;
use Illuminate\Support\Collection;

class GetPublishedCoursesGroupedByTypeAction
{
    /**
     * @return array{liveProgram: Collection, hybrid: Collection, recorded: Collection}
     */
    public function handle(): array
    {
        $courses = Course::query()
            ->where('is_published', true)
            ->orderBy('price_cents')
            ->get()
            ->groupBy('type');

        return [
            'liveProgram' => $courses->get(CourseType::LiveProgram->value, collect()),
            'hybrid' => $courses->get(CourseType::Hybrid->value, collect()),
            'recorded' => $courses->get(CourseType::Recorded->value, collect()),
        ];
    }
}
