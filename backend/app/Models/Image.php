<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'url',
        'source',
        'position',
        'attachment_id',
    ];

    /**
     * Перечисление возможных источников изображений.
     * 
     * @var array<string>
     */
    public const SOURCES = [
        'market' => 'market',
        'yandex' => 'yandex',
        'admin' => 'admin'
    ];

    /**
     * Получить продукт, которому принадлежит изображение.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
} 