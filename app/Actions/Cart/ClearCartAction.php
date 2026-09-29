<?php

namespace App\Actions\Cart;

class ClearCartAction
{
    public function handle(): void
    {
        session()->forget('cart');
    }
}
