<?php

namespace App\Actions\Cart;

use App\Models\Course;
use Illuminate\Support\Collection;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Exception\ApiErrorException;

class GetCheckoutSessionSummaryAction
{
    /**
     * @return array{courses: Collection<int, Course>, totalCents: int, email: ?string}|null
     */
    public function handle(?string $sessionId): ?array
    {
        if (blank($sessionId)) {
            return null;
        }

        try {
            $session = StripeCheckoutSession::retrieve([
                'id' => $sessionId,
                'expand' => ['line_items'],
            ]);
        } catch (ApiErrorException) {
            return null;
        }

        $courseIds = array_filter(array_map('intval', explode(',', $session->metadata->course_ids ?? '')));

        $courses = Course::query()->whereIn('id', $courseIds)->get();

        if ($courses->isEmpty()) {
            return null;
        }

        return [
            'courses' => $courses,
            'totalCents' => $session->amount_total,
            'email' => $session->customer_details->email ?? null,
        ];
    }
}
