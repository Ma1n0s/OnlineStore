<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;

class News extends Model
{
    use HasFactory, Attachable;

    protected $fillable = [
        'title',
        'image',
        'description',
        'tags'
    ];

    protected $casts = [
        'tags' => 'array',
    ];
    
    /**
     * Получить содержимое новости
     * Для обратной совместимости возвращает поле description
     * 
     * @return string|null
     */
    public function getContent()
    {
        return $this->description;
    }

    /**
     * Получить URL изображения новости
     * 
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        // Проверяем, является ли image ID вложения
        if (!empty($this->image) && is_numeric($this->image)) {
            $attachment = Attachment::find($this->image);
            return $attachment ? $attachment->url : null;
        }
        
        // Проверяем, является ли image путем к файлу
        if (!empty($this->image)) {
            return asset('storage/' . $this->image);
        }
        
        return null;
    }
}