<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Filters\Filterable;
use Orchid\Attachment\Models\Attachment;
use Orchid\Attachment\Attachable;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, Filterable, Attachable;

    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'article',
        'slug',
        'brand',
        'rating',
        'category_id',
        'subcategory_id',
        'specifications',
        'images',
        'warranty',
        'advantages',
        'specificationsB',
        'reviews_count',
        'questions_count',
        'slug',
        'old_price',
        'short_description',
        'in_stock',
        'is_featured',
        'sku',
        'barcode',
        'quantity',
    ];

    /**
     * Boot the model.
     */


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::deleting(function ($product) {
            foreach ($product->attachments as $attachment) {
                $attachment->delete();
            }
        });
    }


    /**
     * Получить категорию продукта.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getContent()
    {
        return $this->description; 
    }
    

    /**
     * Получить подкатегорию продукта.
     */
    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
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
    // public function getSpecificationsAttribute(): array
    // {
    //     $result = [];
        
    //     foreach ($this->specificationCategories as $category) {
    //         $categoryName = $category->name;
    //         $result[$categoryName] = $category->specifications->pluck('value', 'name')->toArray();
    //     }
        
    //     return $result;
    // }
    
    protected $casts = [
        'price' => 'float',
        'old_price' => 'float',
        'rating' => 'float',
        'quantity' => 'integer',
        'specifications' => 'array',
        'advantages' => 'array',
        'specificationsB' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
} 