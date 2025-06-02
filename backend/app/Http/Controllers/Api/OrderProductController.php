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
    public function updateAllSelected(Request $request, Order $order)
    {

        $validated = $request->validate([
            'selected' => 'required|boolean'
        ]);
    
        DB::transaction(function () use ($order, $validated) {
            // 1. Обновляем selected у всех продуктов заказа
            DB::table('order_products')
                ->where('order_id', $order->id)
                ->update(['selected' => $validated['selected']]);
            
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
    
    // Добавить продукт в заказ
    // public function store(Request $request, Order $order)
    // {
    //     $validated = $request->validate([
    //         'product_id' => [
    //             'required',
    //             'exists:products,id',
    //             Rule::unique('order_products')->where('order_id', $order->id)
    //         ],
    //         'quantity' => [
    //             'required',
    //             'integer',
    //             'min:1',
    //             function ($attribute, $value, $fail) use ($request) {
    //                 $product = Product::find($request->product_id);
    //                 if ($product && $value > $product->quantity) {
    //                     $fail('Количество не может превышать доступное количество продукта.');
    //                 }
    //             }
    //         ]
    //     ]);

    //     $product = Product::find($validated['product_id']);

    //     $orderProduct = $order->products()->attach($product->id, [
    //         'quantity' => $validated['quantity'],
    //         'price_at_order' => $product->price
    //     ]);

    //     // Обновляем общую сумму заказа
    //     $order->updateTotalAmount();

    //     return response()->json($order->load('products'), 201);
    // }


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
                if ($product && $value > $product->quantity) {
                    $fail('Количество не может превышать доступное количество продукта.');
                }
            }
        ]
    ]);

    $product = Product::find($validated['product_id']);

    // Используем create для промежуточной модели
    $orderProduct = OrderProduct::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => $validated['quantity'],
        'price_at_order' => $product->price,
        'selected' => false // или true, по вашему усмотрению
    ]);

    // Обновляем общую сумму заказа
    $order->updateTotalAmount();

    return response()->json($order->load('products'), 201);
}

    // Обновить количество продукта в заказе
    public function update(Request $request, Order $order, Product $product)
    {
        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($product) {
                    if ($value > $product->quantity) {
                        $fail('Количество не может превышать доступное количество продукта.');
                    }
                }
            ]
        ]);

        $order->products()->updateExistingPivot($product->id, [
            'quantity' => $validated['quantity']
        ]);

        // Обновляем общую сумму заказа
        $order->updateTotalAmount();

        return response()->json($order->load('products'));
    }

    // Удалить продукт из заказа
    public function destroy(Order $order, Product $product)
    {
        $order->products()->detach($product->id);

        // Обновляем общую сумму заказа
        $order->updateTotalAmount();

        return response()->json(null, 204);
    }
}