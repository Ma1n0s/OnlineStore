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


    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->withPivot('quantity', 'price_at_order', 'selected')
            ->withTimestamps();
    }

    public function updateTotalAmount()
    {
        // $total = $this->products->sum(function ($product) {
        //     return $product->pivot->quantity * $product->pivot->price_at_order;
        // });

        // $this->update(['total_amount' => $total]);
    }

    protected $casts = [
        'products' => 'array',
        'is_paid' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
