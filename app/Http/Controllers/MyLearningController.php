<?php

namespace App\Http\Controllers;

use App\Actions\Courses\GetActiveCoursesForUserAction;
use App\Actions\Courses\ResolveVimeoEmbedUrlAction;
use App\Models\Course;
use Illuminate\Http\Request;

class MyLearningController extends Controller
{
    public function index(Request $request, GetActiveCoursesForUserAction $action)
    {
        return view('learning.index', [
            'courses' => $action->handle($request->user()),
        ]);
    }

    public function show(Request $request, Course $course, ResolveVimeoEmbedUrlAction $resolver)
    {
        abort_unless($request->user()->hasAccessTo($course), 403);

        return view('learning.show', [
            'course' => $course,
            'embedUrl' => $resolver->handle($course->vimeo_id),
        ]);
    }
}
