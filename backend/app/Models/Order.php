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
        'products'
    ];

    protected $casts = [
        'products' => 'array',
        'is_paid' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
