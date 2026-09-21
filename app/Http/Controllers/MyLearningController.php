<?php

namespace App\Http\Controllers;

use App\Actions\Courses\GetActiveCoursesForUserAction;
use App\Actions\Courses\GetCourseOutlineAction;
use App\Actions\Courses\ResolveVimeoEmbedUrlAction;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class MyLearningController extends Controller
{
    public function index(Request $request, GetActiveCoursesForUserAction $action)
    {
        return view('learning.index', [
            'courses' => $action->handle($request->user()),
        ]);
    }

    public function show(Request $request, Course $course, ResolveVimeoEmbedUrlAction $resolver, GetCourseOutlineAction $outlineAction)
    {
        abort_unless($request->user()->hasAccessTo($course), 403);

        $course->setRelation('modules', $outlineAction->handle($course));

        return view('learning.show', [
            'course' => $course,
            'embedUrl' => $resolver->handle($course->vimeo_id),
        ]);
    }

    public function lesson(Request $request, Course $course, Lesson $lesson, ResolveVimeoEmbedUrlAction $resolver, GetCourseOutlineAction $outlineAction)
    {
        abort_unless($request->user()->hasAccessTo($course), 403);

        $outline = $outlineAction->handle($course);
        $course->setRelation('modules', $outline);

        [$previous, $next] = $outlineAction->findAdjacentLessons($outline, $lesson);

        return view('learning.lesson', [
            'course' => $course,
            'lesson' => $lesson,
            'embedUrl' => $resolver->handle($lesson->vimeo_id),
            'previous' => $previous,
            'next' => $next,
        ]);
    }
}
