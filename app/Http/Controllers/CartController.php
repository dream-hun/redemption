<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string',
            'price' => 'required|integer|min:0', // Ensure integer
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $validatedData = $validator->validated();
            $sanitizedDomain = strip_tags($validatedData['domain']); // Sanitize!
            $price = (int) $request->input('price'); // Cast to integer

            $cartItem = Cart::create([
                'domain' => $sanitizedDomain, // Use Sanitized Domain
                'price' => $price, // Use integer price
            ]);

            Log::info('Domain added to cart:', ['domain' => $cartItem->domain]);

            return response()->json(['message' => 'Domain added to cart successfully!'], 201);
        } catch (Exception $e) {
            Log::error('Error adding domain to cart:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Failed to add domain to cart.'], 500);
        }
    }
}
