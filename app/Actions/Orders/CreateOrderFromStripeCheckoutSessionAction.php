<?php

namespace App\Actions\Orders;

use App\Actions\Courses\GrantCourseAccessAction;
use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Stripe\Checkout\Session as StripeCheckoutSession;

class CreateOrderFromStripeCheckoutSessionAction
{
    public function __construct(private readonly GrantCourseAccessAction $grantCourseAccessAction) {}

    public function handle(object $session): ?Order
    {
        if (Order::where('stripe_checkout_session_id', $session->id)->exists()) {
            return null;
        }

        $email = $session->customer_details->email;
        $name = $session->customer_details->name ?: $email;
        $courseIds = array_map('intval', explode(',', $session->metadata->course_ids));
        $loggedInUserId = filled($session->metadata->user_id ?? null) ? (int) $session->metadata->user_id : null;

        $expandedSession = StripeCheckoutSession::retrieve([
            'id' => $session->id,
            'expand' => ['line_items'],
        ]);
        $lineItems = collect($expandedSession->line_items->data)->values();

        [$order, $isNewUser, $user] = DB::transaction(function () use ($session, $email, $name, $courseIds, $lineItems, $loggedInUserId) {
            $user = $loggedInUserId ? User::find($loggedInUserId) : null;
            $isNewUser = false;

            if (! $user) {
                $user = User::where('email', $email)->first();
                $isNewUser = $user === null;

                if ($isNewUser) {
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => Str::random(40),
                    ]);

                    $user->forceFill(['email_verified_at' => now()])->save();
                }
            }

            $order = Order::create([
                'user_id' => $user->id,
                'stripe_payment_intent_id' => $session->payment_intent,
                'stripe_checkout_session_id' => $session->id,
                'status' => OrderStatus::Paid,
                'total_cents' => $session->amount_total,
                'currency' => $session->currency,
                'paid_at' => now(),
            ]);

            $courses = Course::query()->whereIn('id', $courseIds)->get()->keyBy('id');

            foreach ($courseIds as $index => $courseId) {
                $course = $courses->get($courseId);
                $priceCents = $lineItems->get($index)?->amount_total ?? $course->price_cents;

                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $course->id,
                    'price_cents' => $priceCents,
                ]);

                $this->grantCourseAccessAction->handle($user, $course, $order);
            }

            return [$order, $isNewUser, $user];
        });

        if ($isNewUser) {
            Password::sendResetLink(['email' => $user->email]);
        }

        return $order;
    }
}
