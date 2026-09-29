<?php

namespace App\Http\Controllers;

use App\Actions\Cart\AddCourseToCartAction;
use App\Actions\Cart\ClearCartAction;
use App\Actions\Cart\GetCartCoursesAction;
use App\Actions\Cart\GetCheckoutSessionSummaryAction;
use App\Actions\Cart\RemoveCourseFromCartAction;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Stripe\Checkout\Session as StripeCheckoutSession;

class CartController extends Controller
{
    public function index(GetCartCoursesAction $action): View
    {
        $courses = $action->handle();

        return view('cart.index', [
            'courses' => $courses,
            'totalCents' => $courses->sum('price_cents'),
        ]);
    }

    public function add(Request $request, Course $course, AddCourseToCartAction $action): RedirectResponse
    {
        abort_unless($course->is_published, 404);

        $user = $request->user();

        if ($user && ! $course->allows_repeat_purchase && $user->hasAccessTo($course)) {
            return redirect()->route('cart.index')->with('error', 'Ya tienes acceso a este curso.');
        }

        $action->handle($course->id);

        return redirect()->route('cart.index');
    }

    public function remove(Course $course, RemoveCourseFromCartAction $action): RedirectResponse
    {
        $action->handle($course->id);

        return redirect()->route('cart.index');
    }

    public function pay(Request $request, GetCartCoursesAction $getCartCourses): RedirectResponse
    {
        $courses = $getCartCourses->handle();

        if ($courses->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $user = $request->user();

        $metadata = [
            'course_ids' => $courses->pluck('id')->implode(','),
        ];

        if ($user) {
            $metadata['user_id'] = (string) $user->id;
        }

        $params = [
            'mode' => 'payment',
            'line_items' => $courses->map(fn (Course $course): array => [
                'quantity' => 1,
                'price_data' => [
                    'currency' => $course->currency,
                    'unit_amount' => $course->price_cents,
                    'product_data' => [
                        'name' => $course->title,
                    ],
                ],
            ])->all(),
            'metadata' => $metadata,
            'success_url' => route('cart.thanks').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('cart.index'),
        ];

        if ($user) {
            $params['customer_email'] = $user->email;
        }

        $session = StripeCheckoutSession::create($params);

        return redirect($session->url);
    }

    public function thanks(Request $request, ClearCartAction $clearCartAction, GetCheckoutSessionSummaryAction $summaryAction): View
    {
        $clearCartAction->handle();

        $sessionId = $request->query('session_id');
        $summary = $summaryAction->handle(is_string($sessionId) ? $sessionId : null);

        return view('cart.thanks', ['summary' => $summary]);
    }
}
