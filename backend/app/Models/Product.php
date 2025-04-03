<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'product_url',
        'search_market_url',
        'search_images_url',
        'created_at',
        'category_id',
        'subcategory_id',
    ];

    /**
     * Получить категорию продукта.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Получить подкатегорию продукта.
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Получить категории спецификаций для продукта.
     */
    public function specificationCategories(): HasMany
    {
        return $this->hasMany(SpecificationCategory::class);
    }

    /**
     * Получить изображения для продукта.
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Получить все спецификации продукта через категории.
     * 
     * @return array
     */
    public function getSpecificationsAttribute(): array
    {
        $result = [];
        
        foreach ($this->specificationCategories as $category) {
            $categoryName = $category->name;
            $result[$categoryName] = $category->specifications->pluck('value', 'name')->toArray();
        }
        
        return $result;
    }
} 