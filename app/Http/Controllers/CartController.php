<?php

namespace App\Http\Controllers;

class CartController extends Controller
{
    public function cart()
    {

        return response()->view('domains.cart');

    }
}
