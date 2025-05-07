<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'icon',
        'description',
        'link',
        'is_special',
        'tags',
        'sort_order'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_special' => 'boolean'
    ];
}