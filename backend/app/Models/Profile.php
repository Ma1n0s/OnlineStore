<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Attachment\Attachable;

class Profile extends Model
{
    use HasFactory, Filterable, Attachable;

    protected $fillable = [
        'user_id',
        'last_name',
        'patronymic',
        'company_name',
        'inn',
        'kpp',
        'legal_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}