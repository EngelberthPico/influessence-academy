<?php

namespace App\Http\Controllers;

use App\Actions\Courses\GetActiveCoursesForUserAction;
use App\Actions\Courses\GetCourseOutlineAction;
use App\Actions\Courses\GetCourseSchedulingAction;
use App\Actions\Courses\GetGuidePageUrlsAction;
use App\Actions\Courses\ResolveVimeoEmbedUrlAction;
use App\Enums\CourseType;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MyLearningController extends Controller
{
    public function index(Request $request, GetActiveCoursesForUserAction $action)
    {
        return view('learning.index', [
            'courses' => $action->handle($request->user()),
        ]);
    }

    public function show(Request $request, Course $course, ResolveVimeoEmbedUrlAction $resolver, GetCourseOutlineAction $outlineAction, GetCourseSchedulingAction $schedulingAction, GetGuidePageUrlsAction $guidePageUrlsAction)
    {
        abort_unless($request->user()->hasAccessTo($course), 403);

        if ($course->type === CourseType::Guide) {
            return view('learning.guide', [
                'course' => $course,
                'pageUrls' => $guidePageUrlsAction->handle($course),
            ]);
        }

        $course->setRelation('modules', $outlineAction->handle($course));

        return view('learning.show', [
            'course' => $course,
            'embedUrl' => $resolver->handle($course->vimeo_id),
            'scheduling' => in_array($course->type, [CourseType::LiveProgram, CourseType::Hybrid], true)
                ? $schedulingAction->handle($request->user(), $course)
                : null,
            'calendlySchedulingUrl' => config('services.calendly.scheduling_url'),
        ]);
    }

    public function guidePage(Request $request, Course $course, int $page)
    {
        abort_unless($request->user()->hasAccessTo($course), 403);
        abort_unless($course->type === CourseType::Guide, 404);

        $path = ($course->guide_pages ?? [])[$page - 1] ?? null;

        abort_unless($path !== null, 404);

        return Storage::disk('local')->response($path, null, ['Cache-Control' => 'private, no-store']);
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
