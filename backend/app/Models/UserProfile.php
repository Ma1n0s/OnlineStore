<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'patronymic',
        'company_name',
        'inn',
        'kpp',
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