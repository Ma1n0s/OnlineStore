<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
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
        'weight',
        'delivery_days',
        'type',
        'category_id',
        'warranty',
        'bonuses',
        'quantity',
        'reviews_count',
        'questions_count',
        'type',
        'delivery_time',
    ];

    protected $attributes = [
        'type' => 'instock',
        'delivery_days' => 0,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = $product->slug ?: Str::slug($product->name);
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }


    // protected static function boot()
    // {
    //     parent::boot();

    //     static::deleting(function ($product) {
    //         foreach ($product->attachments as $attachment) {
    //             $attachment->delete();
    //         }

    //         $product->specifications()->delete();
    //         $product->specificationsB()->delete();
    //         $product->advantages()->delete();
    //     });
    // }

    public function getContent()
    {
        return $this->description; 
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products')
            ->using(OrderProduct::class)
            ->withPivot('quantity', 'price_at_order', 'selected')
            ->withTimestamps();
    }
    

    /**
     * Получить подкатегорию продукта.
     */
    public function category(): BelongsTo
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

    public function specifications(): HasMany
    {
        return $this->hasMany(Specification::class)->orderBy('position');
    }

    public function specificationsB(): HasMany
    {
        return $this->hasMany(SpecificationB::class)->orderBy('position');
    }

    public function advantages(): HasMany
    {
        return $this->hasMany(Advantages::class)->orderBy('position');
    }

    /**
     * Получить все спецификации продукта через категории.
     * 
     * @return array
     */
    
    protected $casts = [
        'price' => 'float',
        'rating' => 'float',
        'delivery_days' => 'integer',
    ];

    /**
     * Get all images for the product combining both traditional images and attachments
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getAllImagesAttribute()
    {
        // Get attachment images - using attachments as the primary image source
        $attachmentImages = collect();
        if ($this->exists) {
            try {
                $attachments = $this->attachment()
                    ->select('attachments.id', 'attachments.name', 'attachments.original_name', 
                             'attachments.mime', 'attachments.extension', 'attachments.path', 
                             'attachments.disk', 'attachments.group', 'attachments.sort',
                             'attachments.position') 
                    ->where('group', 'products')
                    ->orderBy('attachments.position')
                    ->get();
                                
                $attachmentImages = $attachments->map(function($attachment) {
                    return [
                        'id' => 'att_' . $attachment->id,
                        'url' => $attachment->url,
                        'type' => 'attachment',
                        'position' => $attachment->position ?? 0,
                        'alt' => $this->name,
                        'sort' => $attachment->position ?? 0
                    ];
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error getting attachment images: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'product_id' => $this->id,
                ]);
            }
        }
        
        // Get traditional images as fallback/legacy
        $traditionalImages = collect();
        if ($this->images !== null) {
            $traditionalImages = $this->images->map(function($image) {
                return [
                    'id' => 'img_' . $image->id,
                    'url' => $image->url,
                    'type' => 'db_image',
                    'position' => $image->position,
                    'sort' => $image->position,
                    'alt' => $image->alt ?? $this->name,
                ];
            });
        }
        
        // Create a combined collection of all images
        $allImages = collect();
        
        // First add attachment images - these are the primary ones
        foreach ($attachmentImages as $image) {
            $allImages->push($image);
        }
        
        // Then add traditional images as fallback
        foreach ($traditionalImages as $image) {
            $allImages->push($image);
        }
        
        // Sort by position field
        return $allImages->sortBy(function($image) {
            return $image['position'] ?? 999;
        });
    }
    
    /**
     * Get the main product image (first image)
     * 
     * @return string|null
     */
    public function getMainImageAttribute()
    {
        // First try to get from attachments since they're the primary image source now
        try {
            if ($this->exists) {
                $attachment = $this->attachment()
                    ->select('attachments.id', 'attachments.name', 'attachments.original_name', 
                             'attachments.mime', 'attachments.extension', 'attachments.path', 
                             'attachments.disk', 'attachments.group', 'attachments.position') 
                    ->where('group', 'products')
                    ->orderBy('attachments.position')
                    ->first();
                
                if ($attachment) {
                    return $attachment->url;
                }
            }
            
            // Then try traditional images as fallback
            $image = $this->images()->orderBy('position')->first();
            if ($image) {
                return $image->url;
            }
            
            return null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error getting main image: ' . $e->getMessage());
            return null;
        }
    }

    public function scopeAvailable($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' (Арт: ' . $this->article . ')';
    }
} 