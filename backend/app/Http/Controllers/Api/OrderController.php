<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendApiRequest;
use App\Models\BonusTransaction;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Category;
use Illuminate\Support\Str;
use Log;
use Validator;
use Illuminate\Http\Response;
class OrderController extends Controller
{
    /**
     * Display a paginated list of authenticated user's orders
     */

     
     public function updateOrderCompany(Request $request){
        $user = $request->user();

        $validated = $request->validate([
            'selectedCompany' => 'integer|nullable',
        ]);

        $cart = $user->cart()->first();

        if (!$cart) {
            return response()->json(['message' => 'Корзина не найдена'], 404);
        }

        $cart->update([
            'selectedCompany' => $validated['selectedCompany'],
        ]);

        return response()->json($cart,200);
     }

     public function updateOrderBonus(Request $request){
        $user = $request->user();

        $validated = $request->validate([
            'checkBonus' => 'required|boolean',
        ]);

        $checkBonus = $validated['checkBonus'];

        $cart = $user->cart()->first();
    
        if (!$cart) {
            return response()->json(['message' => 'Корзина не найдена'], 404);
        }
        
        if (!!$checkBonus) {
            $newBonus = $user->bonus_balance > $cart->amount ? $cart->amount : $user->bonus_balance;

            $cart->update([
                'checkBonus' => $checkBonus,
                'bonuses' => $newBonus,
            ]);

            // $cart->save();
        } else {
            $cart->update([
                'checkBonus' => $checkBonus,
                'bonuses' => 0,
            ]);

            // $cart->save();
        }

        
        $cart->updateOrderProductsPrices();

        // $newBonus = $user->bonus_balance > $cart->amount ? $cart->amount : $user->bonus_balance;
        return $cart;
        
     }

    public function updateMessageOrder(Request $request) {
        $user = $request->user();

        $validated = $request->validate([
            'message' => 'string|nullable',
        ]);

        $cart = $user->cart()->first();

        if (!$cart) {
            return response()->json(['message' => 'Корзина не найдена'], 404);
        }

        $cart->update([
            'message' => $validated['message'],
        ]);

        return response()->json($cart,200);
    }

    public function createOrderFromSelected(Request $request)
    {
        $user = $request->user();

        return DB::transaction(function () use ($user) {
            // 1. Получаем активную корзину с выбранными товарами
            $cart = $user->orders()
                ->with(['orderProducts' => function($query) {
                    $query->where('selected', true)
                        ->with('product');
                }])
                ->where('status', 'created')
                ->first();

            // 2. Если корзина не найдена или нет товаров
            if (!$cart || !$cart->orderProducts || $cart->orderProducts->isEmpty()) {
                return response()->json([
                    'message' => 'Нет активной корзины с выбранными товарами',
                    'error' => 'no_active_cart_or_selected_products'
                ], 422);
            }

            if($user->bonus_balance <= $cart->bonuses){
                $cart->update([
                    'checkBonus'=> false,
                    'bonuses' => max(0,$user->bonus_balance),
                ]);
                
            }

            $cart->updateOrderProductsPrices();

            $prevPrice = $cart->amount;

            // 3. Проверяем доступное количество
            $productsToOrder = [];
            $errors = [];
            $totalWeight = 0;

            foreach ($cart->orderProducts as $orderProduct) {
                $product = $orderProduct->product;
                $availableQuantity = $product->quantity;
                $requestedQuantity = $orderProduct->quantity;

                if ($requestedQuantity > $availableQuantity && $product->type == 'instock') {
                    $errors[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'requested' => $requestedQuantity,
                        'available' => $availableQuantity
                    ];
                    continue;
                }

                $productsToOrder[] = [
                    'product_id' => $product->id,
                    'quantity' => $requestedQuantity,
                    'price_at_order' => $product->price,
                    'selected' => false
                ];

                $totalWeight += ($product->weight ?? 0) * $requestedQuantity;
            }

            // 4. Если есть ошибки по количеству
            if (!empty($errors)) {
                return response()->json([
                    'message' => 'Недостаточно товаров на складе',
                    'errors' => $errors,
                    'error' => 'insufficient_quantity'
                ], 422);
            }

            $cart->updateOrderProductsPrices();
            $new = $cart->amount;

            if($prevPrice !== $new){
                return response()->json([
                    'message' => 'Неверная цена',
                    'error' => 'no_active_cart_or_selected_products'
                ], 422);
            }

            // 5. Создаем новый заказ
            $newOrder = $user->orders()->create([
                'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
                'status' => 'processing',
                'total_amount' => 0,
                'bonuses' => (int)$cart->bonuses,
                'checkBonus' => $cart->checkBonus,
                'weight' => $totalWeight,
                'selectedCompany' => $cart->selectedCompany,
                'message' => $cart->message,
            ]);

            // 6. Добавляем товары в заказ
            $newOrder->orderProducts()->createMany($productsToOrder);

            // 7. Обновляем сумму заказа
            $newOrder->updateOrderProductsPrices();

            // 8. Удаляем перенесенные товары из корзины
            $cart->orderProducts()
                ->where('selected', true)
                ->delete();

            // 9. Обновляем сумму в корзине
            $cart->updateOrderProductsPrices();

            $newOrder->load('orderProducts.product');
            $cart->fresh()->load('orderProducts.product');

            $cart->message = '';

            $cart->updateOrderProductsPrices();
            
            $this->sync($newOrder);
            $this->processCompletedOrder($newOrder);

            return response()->json([
                'message' => 'Заказ успешно создан',
                'order' => $newOrder,
                'cart' => $cart,
            ], 201);
        });
    }

    public function sync(Order $order){
        
        $order->load('user.companies');
    
        SendApiRequest::dispatch(
            config('services.external_url').'/hs/order',
            $order->toArray(),
            ['Authorization' => config('services.external_token')],
            $order->id
        )->onQueue('api-requests');
    }

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
                'status' => 'created',
                'total_amount' => 0,
                'amount' => 0,
                'bonuses' => 0,
                'checkBonus' => false,
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
            'status' => 'sometimes|in:pending,processing,completed,cancelled,created',
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
            ->where('status', 'created')
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
                'status' => 'created',
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
            'products.*.quantity' => 'required_with:products|integer|min:1',
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

    public function setProId(Request $request, Order $order) {
        $validated = $request->validate([
            'id' => 'required|string',       // pro_id из внешней системы
        ]);
    
        $token = $request->header('Authorization');
    
        if($token !== env('services.external_api.token', '')) {
            return response()->json(['error'=>'auth error'], 401);
        }
    
        if (!$order) {
            Log::error('Webhook error: Order not found', $validated);
            return response()->json(['error' => 'Order not found'], 404);
        }
        
        try {
            $order->update([
                'pro_id' => $validated['id'],
            ]);
            
            Log::info('Webhook processed successfully', [
                'order_id' => $order->id,
                'pro_id' => $validated['id']
            ]);
    
            // 4. Ответ для внешней системы
            return response()->json([
                'status' => 'success',
                'message' => 'Order updated',
                'order_number' => $order->order_number
            ]);
    
        } catch (\Exception $e) {
            Log::error('Webhook update failed', [
                'error' => $e->getMessage(),
                'request' => $validated
            ]);
            
            return response()->json(['error' => 'Update failed'], 500);
        }
    }

    public function updateOrder(Request $request, $order)
    {

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|string|in:pending,processing,completed,cancelled',
            'is_paid' => 'sometimes|boolean',
            'order_number' => 'sometimes|string|max:50',
            'total_amount' => 'sometimes|numeric|min:0',
            'bonuses' => 'sometimes|numeric|min:0',
            'amount' => 'sometimes|numeric|min:0',
            'weight' => 'sometimes|numeric|min:0',
            'products' => 'sometimes|array',
            'products.*.product_id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|integer|min:1',
            'products.*.price_at_order' => 'required_with:products|numeric|min:0.01'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ],500);
        }

        $validated = $validator->validated();

        $token = $request->header('Authorization');

        if($token !== env('services.external_api.token', '')) {
            return response()->json(['error'=>'auth error'], 401);
        }

        $order = Order::where('pro_id', $order)->first();

        return DB::transaction(function () use ($order, $validated) {
            // 1. Обновляем основные данные заказа
            $order->update([
                'status' => $validated['status'] ?? $order->status,
                'is_paid' => $validated['is_paid'] ?? $order->is_paid,
                'order_number' => $validated['order_number'] ?? $order->order_number,
                'total_amount' => $validated['total_amount'] ?? $order->total_amount,
                'bonuses' => $validated['bonuses'] ?? $order->bonuses,
                'amount' => $validated['amount'] ?? $order->amount,
                'weight' => $validated['weight'] ?? $order->weight,
            ]);
    
            // 2. Обновляем продукты в корзине (если переданы)
            if (isset($validated['products'])) {
                $this->updateOrderProducts($order, $validated['products']);
            }


            if ($order->bonuses == 0 && $order->status === 'completed') {
                $newBonuses = max(0,round($order->amount * 0.03));
                
                $this->createBonusTransaction(
                    $order->user_id,
                    $order->id,
                    'Начисление',
                    $newBonuses,
                    'Начисление бонусов за заказ #' . $order->order_number
                );
                
                // Увеличиваем бонусы пользователя
                $order->user->increment('bonus_balance', $newBonuses);
            }

            $order->updateTotalAmount();
    
            return response()->json([
                'message' => 'Корзина успешно обновлена',
                'order' => $order->load('products'),
            ]);
        });


    }
    
    protected function updateOrderProducts(Order $order, array $products)
    {
        $productsToSync = [];
        $productsToUpdate = [];
        $productIds = collect($products)->pluck('product_id');
        
        // Предзагрузка всех продуктов для оптимизации
        $existingProducts = Product::whereIn('id', $productIds)
            ->get()
            ->keyBy('id');
        
        foreach ($products as $productData) {
            if (!isset($existingProducts[$productData['product_id']])) {
                continue;
            }
            
            $product = $existingProducts[$productData['product_id']];
            
            if ($productData['quantity'] > $product->quantity && $product->type === 'В наличии') {
                throw new \Exception(
                    "Not enough stock for product {$product->name}. ".
                    "Requested: {$productData['quantity']}, Available: {$product->quantity}"
                );
            }
            
            $productsToSync[$product->id] = [
                'quantity' => $productData['quantity'],
                'price_at_order' => $productData['price_at_order'],
                'updated_at' => now()
            ];
            
            // Собираем продукты для обновления количества
            if ($product->type === 'instock') {
                $productsToUpdate[] = [
                    'product' => $productData['product_id'],
                    'quantity' => $productData['quantity']
                ];
            }
        }
        
        // Синхронизируем продукты в заказе
        $order->products()->sync($productsToSync);
        
        // Обновляем количество продуктов "В наличии"
        if ($order->status === 'completed') {
            foreach ($productsToUpdate as $item) {
                Product::where('id', $item['product'])
                    ->decrement('quantity', $item['quantity']);
            }

            
        }
    }

    protected function processCompletedOrder(Order $order)
    {
        // Если бонусы использовались - создаем транзакцию на списание

        $user = $order->user;
        if($user->bonus_balance < 0){
            $user->update([
            'bonus_balance'=> 0
            ]);
        }


        if ($order->bonuses > 0) {
            if($user->bonus_balance > 0 ){

                $this->createBonusTransaction(
                    $order->user_id,
                    $order->id,
                    'Списание',
                    -max(0, round($order->bonuses)),
                    'Списание бонусов за заказ #' . $order->order_number
                );
                
                // Уменьшаем бонусы пользователя
                $order->user->decrement('bonus_balance', max(0,round($order->bonuses)));
            }
            
        }
        
        // Начисляем новые бонусы (3% от итоговой суммы), если не использовались бонусы

        if($user->bonus_balance < 0){
            $user->update([
            'bonus_balance'=> 0
            ]);
        }
    }


    protected function createBonusTransaction($userId, $orderId, $operation, $amount, $description = null)
    {
        return BonusTransaction::create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'date' => now()->toDateString(),
            'operation' => $operation,
            'amount' => $amount,
            'status' => 'Завершено',
            'description' => $description,
        ]);
    }
}