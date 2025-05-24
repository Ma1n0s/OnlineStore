<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Feedback extends Model
{
    use HasFactory, AsSource, Filterable;

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'rating',
        'contact_method',
        'status',
        'admin_notes',
    ];

    protected $allowedFilters = [
        'subject',
        'status',
        'created_at',
    ];

    protected $allowedSorts = [
        'subject',
        'status',
        'created_at',
        'rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function statuses(): array
    {
        return [
            'new' => 'Новый',
            'in_progress' => 'В обработке',
            'resolved' => 'Решен',
        ];
    }
}
