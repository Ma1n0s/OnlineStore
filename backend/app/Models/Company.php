<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'inn',
        'kpp',
        'legal_address',
        'director',
        'phone',
        'email',
        'is_main'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}