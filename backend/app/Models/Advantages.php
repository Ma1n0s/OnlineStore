<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advantages extends Model
{
    protected $table = 'advantages';
    
    protected $fillable = [
        'product_id',
        'title',
        'description',
        'position'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}