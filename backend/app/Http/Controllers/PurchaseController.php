<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function processPurchase(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|integer',
            'products.*.name' => 'required|string',
            'products.*.price' => 'required|numeric',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $application = Application::create([
            'user_id' => $user->id,
            'title' => 'Заказ #' . time(),
            'description' => 'Заказ от ' . $user->name,
            'status' => 'pending',
            'amount' => $this->calculateTotal($validated['products']),
            'products' => $validated['products']
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Заказ успешно оформлен',
            'application_id' => $application->id
        ]);
    }
    
    protected function calculateTotal(array $products): float
    {
        $total = 0;
        
        foreach ($products as $product) {
            $total += $product['price'] * $product['quantity'];
        }
        
        return $total;
    }
}