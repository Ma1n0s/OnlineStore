<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();
        
        return response()->json([
            'cartItems' => $cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'options' => $item->options
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
            'options' => 'sometimes|array'
        ]);

        $cartItem = Auth::user()->cartItems()->updateOrCreate(
            ['product_id' => $request->product_id],
            [
                'quantity' => $request->quantity ?? 1,
                'options' => $request->options ?? null
            ]
        );

        return response()->json($cartItem->load('product'), 201);
    }

    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->delete();

        return response()->noContent();
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'sometimes|integer|min:1',
            'options' => 'sometimes|array'
        ]);

        $cartItem->update($request->only(['quantity', 'options']));

        return response()->json($cartItem->load('product'));
    }
}