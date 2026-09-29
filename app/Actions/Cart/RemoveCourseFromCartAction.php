<?php

namespace App\Actions\Cart;

class RemoveCourseFromCartAction
{
    public function handle(int $courseId): void
    {
        $cart = array_values(array_diff(session('cart', []), [$courseId]));

        session(['cart' => $cart]);
    }
}
