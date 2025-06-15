<?php

namespace App\Models;

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
        'status',
        'bonuses',
        'amount',
        'weight',
        'is_paid',
        'selected',
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
