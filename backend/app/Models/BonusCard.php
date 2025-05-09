<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_number',
        'current_level',
        'max_level',
        'points',
        'points_to_next_level'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generateCardNumber()
    {
        return 'BC-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }
}