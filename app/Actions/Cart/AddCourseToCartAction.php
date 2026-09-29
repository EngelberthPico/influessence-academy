<?php

namespace App\Actions\Cart;

class AddCourseToCartAction
{
    public function handle(int $courseId): void
    {
        $cart = session('cart', []);

        if (! in_array($courseId, $cart, true)) {
            $cart[] = $courseId;
        }

        session(['cart' => $cart]);
    }
}
