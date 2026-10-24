<?php

namespace App\Http\Controllers;

use App\Actions\Orders\CreateOrderFromStripeCheckoutSessionAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        $session = $event->data->object;

        // La misma cuenta de Stripe recibe compras de Influessence Agency (enlaces
        // de pago, sin metadata.course_ids). Esas no son del Academy: se ignoran
        // aquí para no crear nada y no devolver un error que haga reintentar a Stripe.
        if (blank($session->metadata->course_ids ?? null)) {
            Log::info('Checkout session ignorada: no trae course_ids, no es una compra del Academy.', [
                'stripe_checkout_session_id' => $session->id,
            ]);

            return response()->json(['received' => true]);
        }

        $action->handle($session, $event->created);

        return response()->json(['received' => true]);
    }
}
