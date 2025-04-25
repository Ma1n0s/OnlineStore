<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    protected $table = 'specifications';
    
    protected $fillable = [
        'product_id',
        'key',
        'value',
        'position'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}