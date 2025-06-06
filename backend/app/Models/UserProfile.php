<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory, AsSource, Filterable;

    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'patronymic',
        'company_name',
        'inn',
        'kpp',
        'phone',
        'email',
        'legal_address',
        'director',
        'company_phone',
        'company_email'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}