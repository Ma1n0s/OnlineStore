<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class SwiperItem extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image',
        'link',
        'position',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'image' => 'string',
    ];
    
    /**
     * Get image attribute
     *
     * @param mixed $value
     * @return string|null
     */
    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Убедимся, что путь начинается с / если это локальный путь и не начинается с http
        if (!str_starts_with($value, '/') && !str_starts_with($value, 'http')) {
            $value = '/' . $value;
        }
        
        return $value;
    }
    
    /**
     * Set image attribute
     *
     * @param mixed $value
     * @return void
     */
    public function setImageAttribute($value)
    {
        $this->attributes['image'] = $value;
    }
}
