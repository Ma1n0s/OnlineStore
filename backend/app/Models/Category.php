<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Orchid\Attachment\Models\Attachment;

class Category extends Model
{
    use HasFactory, AsSource, Filterable, Attachable,NodeTrait;

    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'pro_id',
        'name',
        'title',
        'description',
        'slug',
        'image_url',
        'description_image_url',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Получить родительскую категорию.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getHasProductsAttribute()
    {
        return $this->products()->exists();
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function canHaveSubcategories()
    {
        return $this->type === 'category' && !$this->has_products;
    }

    public function canHaveProducts()
    {
        return $this->type === 'product_container' || $this->children()->exists();
    }

    /**
     * Получить прямые дочерние категории.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Получить все дочерние категории (рекурсивно).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Получить все родительские категории (рекурсивно).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ancestors()
    {
        $ancestors = collect();
        $current = $this;
        
        while ($current->parent) {
            $current = $current->parent;
            $ancestors->prepend($current);
        }
        
        return $ancestors;
    }

    public function parentRecursive()
    {
        return $this->belongsTo(Category::class, 'parent_id')->with('parentRecursive');
    }

    /**
     * Получить продукты в этой категории.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    /**
     * Получить корневые категории (без родителя).
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function roots()
    {
        return static::whereNull('parent_id')->get();
    }

    protected static function booted()
    {
        static::deleting(function ($category) {
            if ($category->image_url) {
                Storage::disk('public')->delete($category->image_url);
            }
            if ($category->description_image_url) {
                Storage::disk('public')->delete($category->description_image_url);
            }
        });
    }

    /**
     * Получить полный путь категории от корня.
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getPath()
    {
        $path = collect([$this]);
        $current = $this;
        
        while ($current->parent) {
            $path->prepend($current->parent);
            $current = $current->parent;
        }
        
        return $path;
    }

    /**
     * Проверить, является ли категория корневой.
     * 
     * @return bool
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Проверить, имеет ли категория дочерние элементы.
     * 
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get the category's main image
     * 
     * @return string|null
     */
    public function getMainImageAttribute()
    {
        try {
            // First try to get from image_url field
            if (!empty($this->image_url)) {
                // Check if it's an attachment ID
                if (is_numeric($this->image_url)) {
                    $attachment = Attachment::find($this->image_url);
                    return $attachment ? $attachment->url : null;
                }
                return $this->image_url;
            }
            
            // Otherwise try to get from attachments
            if ($this->exists) {
                $attachment = $this->attachment()
                    ->select('attachments.*') // Явно выбираем все поля из таблицы attachments
                    ->where('group', 'category_main')
                    ->first();
                return $attachment ? $attachment->url : null;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting category main image: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get the category's description image
     * 
     * @return string|null
     */
    public function getDescriptionImageAttribute()
    {
        try {
            // First try to get from description_image_url field
            if (!empty($this->description_image_url)) {
                // Check if it's an attachment ID
                if (is_numeric($this->description_image_url)) {
                    $attachment = Attachment::find($this->description_image_url);
                    return $attachment ? $attachment->url : null;
                }
                return $this->description_image_url;
            }
            
            // Otherwise try to get from attachments
            if ($this->exists) {
                $attachment = $this->attachment()
                    ->select('attachments.*') // Явно выбираем все поля из таблицы attachments
                    ->where('group', 'category_description')
                    ->first();
                return $attachment ? $attachment->url : null;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting category description image: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all associated images
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getAllImagesAttribute()
    {
        try {
            $images = collect();
            
            // Add main image if it exists
            if (!empty($this->image_url)) {
                $images->push([
                    'id' => 'main',
                    'url' => $this->getMainImageAttribute(),
                    'type' => 'main_image',
                    'group' => 'category_main',
                    'name' => 'Основное изображение',
                ]);
            }
            
            // Add description image if it exists
            if (!empty($this->description_image_url)) {
                $images->push([
                    'id' => 'description',
                    'url' => $this->getDescriptionImageAttribute(),
                    'type' => 'description_image',
                    'group' => 'category_description',
                    'name' => 'Изображение описания',
                ]);
            }
            
            // Add attachment images if available
            if ($this->exists) {
                $attachmentImages = $this->attachment()
                    ->select('attachments.*') // Явно выбираем все поля из таблицы attachments
                    ->get()
                    ->map(function($attachment) {
                        return [
                            'id' => 'att_' . $attachment->id,
                            'url' => $attachment->url,
                            'type' => 'attachment',
                            'group' => $attachment->group,
                            'name' => $attachment->original_name,
                        ];
                    });
                
                $images = $images->merge($attachmentImages);
            }
            
            return $images;
        } catch (\Exception $e) {
            Log::error('Error getting category all images: ' . $e->getMessage());
            return collect();
        }
    }

    public function getFullPathAttribute(): string
    {
        $path = [];
        $current = $this;

        while ($current) {
            array_unshift($path, $current->name);
            $current = $current->parent;
        }

        return implode(' > ', $path);
    }
} 