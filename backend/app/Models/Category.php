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

class Category extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
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
    public function ancestors(): BelongsTo
    {
        return $this->parent()->with('ancestors');
    }

    /**
     * Получить продукты в этой категории.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    /**
     * Получить корневые категории (без родителя).
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function roots()
    {
        return static::whereNull('parent_id')->get();
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
} 