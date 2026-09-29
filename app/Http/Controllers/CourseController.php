<?php

namespace App\Http\Controllers;

use App\Actions\Courses\GetPublishedCoursesGroupedByTypeAction;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(GetPublishedCoursesGroupedByTypeAction $action)
    {
        $courses = $action->handle();

        return view('courses.index', [
            'liveProgramCourses' => $courses['liveProgram'],
            'hybridCourses' => $courses['hybrid'],
            'recordedCourses' => $courses['recorded'],
        ]);
    }

    public function show(Request $request, Course $course)
    {
        abort_unless($course->is_published, 404);

        $user = $request->user();
        $alreadyHasAccess = $user && ! $course->allows_repeat_purchase && $user->hasAccessTo($course);

        return view('courses.show', ['course' => $course, 'alreadyHasAccess' => $alreadyHasAccess]);
    }
}
