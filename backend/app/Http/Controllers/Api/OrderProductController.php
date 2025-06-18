<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderProductController extends Controller
{
    public function deleteAllSelected(Request $request) {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Требуется авторизация',
                'error' => 'unauthenticated'
            ], 401);
        }
        
        // Получаем активную корзину пользователя
        $cart = $user->cart;
        
        if (!$cart) {
            return response()->json([
                'message' => 'Корзина не найдена',
                'error' => 'cart_not_found'
            ], 404);
        }

        // Удаляем все выбранные продукты из корзины
            $deletedCount = $cart->products()
            ->wherePivot('selected', true)
            ->detach();

        if ($deletedCount > 0) {
            // Обновляем корзину (если нужно пересчитать totals и т.д.)
            $cart->touch(); // Обновляем timestamp корзины
            
            return response()->json([
                'message' => 'Удалено ' . $deletedCount . ' товаров',
                'count' => $deletedCount
            ], 200);
        }

        return response()->json([
            'message' => 'Нет выбранных товаров для удаления',
            'count' => 0
        ], 200);
    }
    public function updateAllSelected(Request $request, Order $order)
    {

        $validated = $request->validate([
            'selected' => 'required|boolean',
            'all' => 'required|boolean'
        ]);

        if($order->user_id != $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }
    
        DB::transaction(function () use ($order, $validated) {
            // 1. Обновляем selected у всех продуктов заказа
            if($validated['all']){
                DB::table('order_products')
                    ->where('order_id', $order->id)
                    ->update(['selected' => $validated['selected']]);
            }
            // 2. Обновляем флаг в самом заказе
            $order->update([
                'selected' => $validated['selected'],
                'updated_at' => now() // Принудительно обновляем метку времени
            ]);
        });

        return response()->json([
            'message' => 'Selected status updated for all products',
        ]);
    }


    public function store(Request $request, Order $order)
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('order_products')->where('order_id', $order->id)
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $product = Product::find($request->product_id);
                    if ($product && $value > $product->quantity && $product->type === 'instock') {
                        $fail('Количество не может превышать доступное количество продукта.');
                    }
                }
            ]
        ]);

        if($order->user_id != $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $product = Product::find($validated['product_id']);

        // Используем create для промежуточной модели
        $orderProduct = OrderProduct::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $validated['quantity'],
            'price_at_order' => $product->price,
            'selected' => true // или true, по вашему усмотрению
        ]);

        // Обновляем общую сумму заказа
        $order->updateTotalAmount();

        return response()->json($order->load('products'), 201);
    }

    // Обновить количество продукта в заказе
    public function update(Request $request, Order $order, Product $product)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Требуется авторизация',
                'error' => 'unauthenticated'
            ], 401);
        }
        
        $cart = $user->cart;
        
        if (!$cart) {
            return response()->json([
                'message' => 'Корзина не найдена',
                'error' => 'cart_not_found'
            ], 404);
        }

        $validated = $request->validate([
            'selected' => ['required', 'boolean'],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($product) {
                    if ($value > $product->quantity && $product->type == 'instock') {
                        $fail('Количество не может превышать доступное количество продукта.');
                    }
                }
            ],
        ]);


        // Подготавливаем данные для обновления
        $updateData = [
            'quantity' => $validated['quantity'],
            'selected' => $validated['selected']
        ];

        // Обновляем товар в корзине
        $cart->products()->updateExistingPivot($product->id, $updateData);

        // Проверяем, все ли товары выбраны
        $allSelected = $cart->products()
            ->wherePivot('selected', false)
            ->doesntExist();

        // Обновляем статус корзины
        $cart->update(['selected' => $allSelected]);

        // Обновляем общую сумму
        $cart->updateTotalAmount();

        return response()->json([
            'cart' => $cart->load('products'),
            'all_selected' => $allSelected
        ]);
    }

    // Удалить продукт из заказа
    public function destroy(Request $request, Order $order, Product $product)
    {

        if($order->user_id != $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $order->products()->detach($product->id);

        // Обновляем общую сумму заказа
        $order->updateTotalAmount();

        return response()->json(null, 204);
    }

    public function index(Request $request)
    {

        $user = $request->user();
        $validated = $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);
        
        // Получаем заказы с пагинацией
        $orders = $user->orders()
            ->with(['orderProducts.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(
                $request->per_page ?? 15,  
                ['*'],                      
                'page',                     
                $request->page ?? 1        
            );
        
        return response()->json($orders);
    }
}