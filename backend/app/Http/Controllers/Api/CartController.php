<?php

namespace App\Http\Controllers\Api;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = $this->getCartItems($request);
        
        return response()->json([
            'cartItems' => $cartItems->load('product'),
            'total' => $this->calculateTotal($cartItems)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
            'options' => 'sometimes|array'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        $cartItem = $this->getUserCart()->firstWhere('product_id', $product->id);
        
        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + ($request->quantity ?? 1),
                'options' => $request->options ?? $cartItem->options
            ]);
        } else {
            $cartItem = CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity ?? 1,
                'options' => $request->options ?? null,
                'session_id' => Auth::check() ? null : $this->getSessionId($request)
            ]);
        }
        
        return response()->json($cartItem->load('product'), 201);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorize('update', $cartItem);
        
        $request->validate([
            'quantity' => 'sometimes|integer|min:1',
            'options' => 'sometimes|array'
        ]);
        
        $cartItem->update($request->only(['quantity', 'options']));
        
        return response()->json($cartItem->load('product'));
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        $this->authorize('delete', $cartItem);
        
        $cartItem->delete();
        
        return response()->noContent();
    }

    public function clear(Request $request)
    {
        $this->getUserCart()->delete();
        
        return response()->noContent();
    }

    protected function getUserCart()
    {
        return Auth::check() 
            ? Auth::user()->cartItems()
            : CartItem::where('session_id', $this->getSessionId(request()));
    }

    protected function getCartItems(Request $request)
    {
        return Auth::check()
            ? Auth::user()->cartItems()->get()
            : CartItem::where('session_id', $this->getSessionId($request))->get();
    }

    protected function calculateTotal($cartItems)
    {
        return $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    }

    protected function getSessionId(Request $request)
    {
        return $request->session()->getId() ?? Str::random(40);
    }
}