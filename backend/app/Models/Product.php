<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Filters\Filterable;
use App\Models\Advantages;
use App\Models\Specification;
use App\Models\SpecificationB;
use Orchid\Attachment\Models\Attachment;
use Orchid\Attachment\Attachable;

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
        'specifications',
        'warranty',
        'advantages',
        'specificationsB',
        'reviews_count',
        'questions_count',
    ];



    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            foreach ($product->attachments as $attachment) {
                $attachment->delete();
            }
        });
    }

    /**
     * Получить категорию продукта.
     */
    // public function category(): BelongsTo
    // {
    //     return $this->belongsTo(Category::class, 'category_id');
    // }

    public function getContent()
    {
        return $this->description; 
    }
    

    /**
     * Получить подкатегорию продукта.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
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

    /**
     * Get all images for the product combining both traditional images and attachments
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getAllImagesAttribute()
    {
        // Get traditional images
        $traditionalImages = collect();
        if ($this->images !== null) {
            $traditionalImages = $this->images->map(function($image) {
                return [
                    'id' => 'img_' . $image->id,
                    'url' => $image->url,
                    'type' => 'db_image',
                    'position' => $image->position,
                    'alt' => $image->alt ?? $this->name,
                ];
            });
        }
        
        // Get attachment images
        $attachmentImages = collect();
        if ($this->exists) {
            try {
                // Явно указываем выборку полей с нужной таблицы, чтобы избежать неоднозначности
                $attachments = $this->attachment()
                    ->select('attachments.*') // Явно выбираем все поля из таблицы attachments
                    ->where('group', 'products')
                    ->get();
                
                $attachmentImages = $attachments->map(function($attachment) {
                    return [
                        'id' => 'att_' . $attachment->id,
                        'url' => $attachment->url,
                        'type' => 'attachment',
                        'position' => 0,
                        'alt' => $this->name,
                    ];
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error getting attachment images: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'product_id' => $this->id,
                ]);
            }
        }
        
        // Combine and sort by position
        return $traditionalImages->merge($attachmentImages)->sortBy('position');
    }
    
    /**
     * Get the main product image (first image)
     * 
     * @return string|null
     */
    public function getMainImageAttribute()
    {
        // First try to get from traditional images
        try {
            $image = $this->images()->orderBy('position')->first();
            if ($image) {
                return $image->url;
            }
            
            // Then try attachments
            if ($this->exists) {
                $attachment = $this->attachment()
                    ->select('attachments.*') // Явно выбираем все поля из таблицы attachments
                    ->where('group', 'products')
                    ->first();
                if ($attachment) {
                    return $attachment->url;
                }
            }
            
            return null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error getting main image: ' . $e->getMessage());
            return null;
        }
    }
} 