<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function getFullOrders(Request $request)
    {
        $validated = $request->validate([
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        try {
            $user = $request->user();
            
            $query = Order::with(['products' => function($query) {
                    $query->select(['id', 'name', 'main_image']);
                }])
                ->where('user_id', $user->id)
                ->where('status', '!=', 'created')
                ->orderBy('created_at', 'desc');

            $perPage = $validated['per_page'] ?? 10;
            $orders = $query->paginate($perPage, ['*'], 'page', $validated['page'] ?? 1);

            return response()->json([
                'data' => $orders->items(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}