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
        'product_id', 
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

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($application) {
            if ($application->isDirty('status') && $application->status === 'completed') {
                $originalStatus = $application->getOriginal('status');
                
                if ($originalStatus !== 'completed') {
                    $application->awardBonuses();
                }
            }
        });
    }

    public function awardBonuses()
    {
        // if ($this->product_id && $this->user_id) {
        //     $product = Product::find($this->product_id);
        //     $user = User::find($this->user_id);
            
        //     if ($product && $user && $product->bonuses > 0) {
        //         $user->bonusTransactions()->create([
        //             'date' => now(),
        //             'operation' => 'Начисление',
        //             'amount' => $product->bonuses,
        //             'status' => 'Завершено',
        //         ]);
        //     }
        // }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}