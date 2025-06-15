<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BonusController extends Controller
{
    public function getBonusTransactions(Request $request)
    {
        $validated = $request->validate([
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort' => 'nullable|string|in:newest,oldest',
        ]);

        try {
            $query = $user->bonusTransactions()
                ->select([
                    'id',
                    'date',
                    'operation',
                    'amount',
                    'status',
                    'created_at'
                ]);

            if ($validated['sort'] === 'newest') {
                $query->latest();
            } else {
                $query->oldest();
            }

            $transactions = $query->paginate(
                $validated['per_page'] ?? 10,
                ['*'],
                'page',
                $validated['page'] ?? 1
            );

            return response()->json([
                'data' => $transactions->items(),
                'meta' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ],
                'bonus_balance' => $user->bonus_balance,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}