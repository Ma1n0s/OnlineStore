<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'is_paid',
        'selected',
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
        $total = $this->orderProducts->sum(function ($orderProduct) {
            return $orderProduct->quantity * $orderProduct->price_at_order;
        });

        $this->update(['total_amount' => $total]);
    }

    protected $casts = [
        'is_paid' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
