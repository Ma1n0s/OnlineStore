<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Orchid\Filters\ApplicationFilter;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Application extends Model
{
    use HasFactory, AsSource, Filterable;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'amount',
        'products'
    ];

    protected $casts = [
        'products' => 'array',
    ];

    protected $allowedFilters = [
        ApplicationFilter::class,
    ];

    protected $allowedSorts = [
        'title',
        'status',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}