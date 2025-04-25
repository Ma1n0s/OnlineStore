<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecificationB extends Model
{
    protected $table = 'specifications_b';
    
    protected $fillable = [
        'product_id',
        'name',
        'value',
        'position'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}