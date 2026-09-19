<?php

namespace App\Http\Controllers;

use App\Actions\Courses\GetPublishedCoursesGroupedByTypeAction;

class HomeController extends Controller
{
    public function index(GetPublishedCoursesGroupedByTypeAction $action)
    {
        $courses = $action->handle();

        return view('welcome', [
            'liveProgramCourses' => $courses['liveProgram'],
            'hybridCourses' => $courses['hybrid'],
            'recordedCourses' => $courses['recorded'],
        ]);
    }
}
