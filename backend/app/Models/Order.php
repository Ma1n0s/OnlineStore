<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, AsSource, Filterable;

    protected $fillable = [
        'user_id',
        'order_number',
        'pro_id',
        'total_amount',
        'checkBonus',
        'status',
        'bonuses',
        'amount',
        'weight',
        'is_paid',
        'selected',
        'selectedCompany',
        'message',
    ];

    protected $with = [
        'products'
    ];

    protected $appends = [
        'products_count'
    ];

    protected $hidden = [
        'orderProducts'
    ];

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->using(OrderProduct::class)
            ->withPivot(['quantity', 'price_at_order', 'selected'])
            ->withTimestamps();
    }

    public function updateOrderProductsPrices()
    {
        return DB::transaction(function () {
            // Получаем все продукты заказа с актуальными ценами
            $products = Product::whereIn('id', function($query) {
                    $query->select('product_id')
                        ->from('order_products')
                        ->where('order_id', $this->id);
                })
                ->select('id', 'price')
                ->get()
                ->keyBy('id');
            
            // Обновляем цены для каждого товара в заказе
            $this->orderProducts()->each(function($orderProduct) use ($products) {
                if (isset($products[$orderProduct->product_id])) {
                    $newPrice = $products[$orderProduct->product_id]->price;
                    
                    // Обновляем только если цена изменилась
                    if ($orderProduct->price_at_order != $newPrice) {
                        $orderProduct->update(['price_at_order' => $newPrice]);
                    }
                }
            });
            
            // Пересчитываем общую сумму заказа
            $this->updateTotalAmount();
            
            return $this;
        });
    }

    public function updateTotalAmount()
    {
        $amount = $this->orderProducts->sum(function ($orderProduct) {
            return $orderProduct->quantity * $orderProduct->price_at_order;
        });
    
        // 2. Получаем текущие бонусы (если они уже были установлены вручную)
        $bonuses = $this->bonuses ?? 0;
        
        // 3. Рассчитываем итоговую сумму к оплате
        $totalAmount = max($amount - $bonuses, 0);
    
        // 4. Обновляем все поля
        $this->update([
            'total_amount' => $totalAmount,
            'bonuses' => $bonuses,
            'amount' => $amount,
        ]);
    
        return $this;
    }

    protected $casts = [
        'is_paid' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bonusTransactions()
    {
        return $this->morphMany(BonusTransaction::class, 'transactionable');
    }

    public function getProductsCountAttribute()
    {
        return $this->products->sum('pivot.quantity');
    }
}
