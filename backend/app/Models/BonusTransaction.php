<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class BonusTransaction extends Model
{
    use AsSource;

    protected $fillable = [
        'user_id',
        'date',
        'operation',
        'amount',
        'status',
        'description'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    protected $allowedFilters = [
        'operation' => Where::class,
        'amount' => Where::class,
        'date' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'date',
        'operation',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}