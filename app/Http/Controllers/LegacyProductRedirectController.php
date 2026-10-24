<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;

class LegacyProductRedirectController extends Controller
{
    /**
     * Slugs del WooCommerce anterior que sí tienen un curso equivalente hoy.
     *
     * @var array<string, string>
     */
    private const SLUG_MAP = [
        '11-class' => 'asesoria-11',
    ];

    public function __invoke(string $slug): RedirectResponse
    {
        $newSlug = self::SLUG_MAP[$slug] ?? null;

        if ($newSlug) {
            $course = Course::query()->where('slug', $newSlug)->where('is_published', true)->first();

            if ($course) {
                return redirect()->route('courses.show', $course, 301);
            }
        }

        return redirect()->route('courses.index', [], 301);
    }
}
