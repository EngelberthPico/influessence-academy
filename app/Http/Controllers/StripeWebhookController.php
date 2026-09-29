<?php

namespace App\Http\Controllers;

use App\Actions\Orders\CreateOrderFromStripeCheckoutSessionAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Exception\UnexpectedValueException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, CreateOrderFromStripeCheckoutSessionAction $action): JsonResponse
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
                config('services.stripe.webhook_secret'),
            );
        } catch (SignatureVerificationException|UnexpectedValueException) {
            return response()->json(['message' => 'Invalid signature.'], 400);
        }

        if ($event->type !== 'checkout.session.completed') {
            return response()->json(['received' => true]);
        }

        $action->handle($event->data->object);

        return response()->json(['received' => true]);
    }
}
