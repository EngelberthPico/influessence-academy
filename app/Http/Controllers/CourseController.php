<?php

namespace App\Http\Controllers;

use App\Enums\CourseType;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::query()
            ->where('is_published', true)
            ->orderBy('price_cents')
            ->get()
            ->groupBy('type');

        return view('courses.index', [
            'liveProgramCourses' => $courses->get(CourseType::LiveProgram->value, collect()),
            'hybridCourses' => $courses->get(CourseType::Hybrid->value, collect()),
            'recordedCourses' => $courses->get(CourseType::Recorded->value, collect()),
        ]);
    }

    public function show(Course $course)
    {
        abort_unless($course->is_published, 404);

        return view('courses.show', ['course' => $course]);
    }
}
