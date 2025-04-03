<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Specification extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'value',
    ];

    /**
     * Получить категорию спецификации.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SpecificationCategory::class, 'category_id');
    }
} 