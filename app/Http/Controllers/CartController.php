<?php

declare(strict_types=1);

namespace App\Http\Controllers;

final class CartController extends Controller
{
    public function cart()
    {

        return response()->view('domains.cart');

    }
}
