<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecificationB extends Model
{
    use HasFactory;

    protected $table = 'specifications_b';

    protected $fillable = [
        'product_id',
        'name',
        'value',
        'category_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}