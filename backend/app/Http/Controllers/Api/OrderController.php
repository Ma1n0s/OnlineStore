<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Display a paginated list of authenticated user's orders
     */

     public function activeCart(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Требуется авторизация',
                'error' => 'unauthenticated'
            ], 401);
        }
        
        // Пытаемся найти активную корзину
        $cart = $user->cart;
        
        // Если нет активной корзины - создаем новую
        if (!$cart) {
            $cart = $user->orders()->create([
                'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
                'status' => 'pending',
                'total_amount' => 0
            ]);
            
            // Инициализируем пустой массив продуктов
            $cart->load(['products', 'user']);
        }

        $cart->load(['products', 'user']);

        return response()->json($cart);
    }

    public function index(Request $request)
    {
        $request->validate([
            'status' => 'sometimes|in:pending,processing,completed,cancelled',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $user = Auth::user();
        
        $query = $user->orders()
            ->with('products')
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 15);
        $orders = $query->paginate($perPage);

        return $this->paginatedResponse($orders, $request);
    }

    /**
     * Create a new order for authenticated user
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Проверяем есть ли у пользователя активный заказ (status = pending)
        $hasActiveOrder = $user->orders()
            ->where('status', 'pending')
            ->exists();
        
        if ($hasActiveOrder) {
            return response()->json([
                'message' => 'У вас уже есть активный заказ. Завершите или отмените текущий заказ перед созданием нового.',
                'error' => 'active_order_exists'
            ], 422);
        }
    
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1'
        ]);
    
        return DB::transaction(function () use ($user, $validated) {
            $order = $user->orders()->create([
                'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
                'status' => 'pending',
                'total_amount' => 0
            ]);
    
            $this->addProductsToOrder($order, $validated['products']);
    
            return response()->json($order->load('products'), 201);
        });
    }

    /**
     * Display specific order of authenticated user
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        
        return $order->load('products');
    }

    /**
     * Update authenticated user's order
     */
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,processing,completed,cancelled',
            'products' => 'sometimes|array',
            'products.*.product_id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|integer|min:1'
        ]);

        return DB::transaction(function () use ($order, $validated) {
            if (isset($validated['status'])) {
                $order->update(['status' => $validated['status']]);
            }

            if (isset($validated['products'])) {
                $order->products()->detach();
                $this->addProductsToOrder($order, $validated['products']);
            }

            return response()->json($order->fresh()->load('products'));
        });
    }

    /**
     * Cancel/delete authenticated user's order
     */
    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        DB::transaction(function () use ($order) {
            $order->products()->detach();
            $order->delete();
        });

        return response()->json(null, 204);
    }

    /**
     * Helper method to add products to order with validation
     */
    protected function addProductsToOrder(Order $order, array $products)
    {
        foreach ($products as $item) {
            $product = Product::findOrFail($item['product_id']);

            if ($product->quantity < $item['quantity']) {
                abort(422, "Not enough stock for product {$product->name}");
            }

            $order->products()->attach($product->id, [
                'quantity' => $item['quantity'],
                'price_at_order' => $product->price
            ]);
        }

        $order->updateTotalAmount();
    }

    /**
     * Format paginated response
     */
    protected function paginatedResponse($paginator, $request)
    {
        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'status_filter' => $request->status ?? 'all'
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl()
            ]
        ]);
    }
}