<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        try {
            $cartItem = Cart::create([
                'domain' => $request->input('domain'),
                'price' => $request->input('price'),
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

    public function updatePeriod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|string|exists:carts,uuid',
            'period' => 'required|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $cartItem = Cart::where('uuid', $request->item_id)->firstOrFail();
            $cartItem->period = $request->period;
            $cartItem->save();

            // Return updated cart totals
            $items = Cart::where(function ($query) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                } else {
                    $query->where('session_id', session()->getId());
                }
            })->get();

            return response()->json([
                'message' => 'Period updated successfully',
                'item' => [
                    'price' => $cartItem->formatedPrice(),
                    'base_price' => $cartItem->getBasePrice(),
                ],
                'cart' => [
                    'subtotal' => $items->sum(function ($item) {
                        return $item->price * $item->period;
                    }),
                    'tax' => $items->sum(function ($item) {
                        return $item->price * $item->period * 0.18;
                    }),
                    'total' => $items->sum(function ($item) {
                        return $item->price * $item->period * 1.18;
                    }),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Error updating period:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Failed to update period.'], 500);
        }
    }

    public function removeItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|string|exists:carts,uuid',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $cartItem = Cart::where('uuid', $request->item_id)->firstOrFail();
            $cartItem->delete();

            return response()->json(['message' => 'Item removed successfully']);
        } catch (Exception $e) {
            Log::error('Error removing item:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Failed to remove item.'], 500);
        }
    }

    public function cart(Request $request)
    {

        $items = Cart::where(function ($query) {
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', session()->getId());
            }
        })->get();

        // If authenticated user and cart is empty, redirect to domains index
        if (Auth::check() && $items->isEmpty()) {
            return redirect()->route('domains.index')->with('info', 'Your cart is empty. Browse available domains.');
        }

        return view('domains.cart', compact('items'));
    }
}
