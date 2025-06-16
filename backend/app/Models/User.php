<?php

namespace App\Models;

use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'bonus_balance',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
           'id'         => Where::class,
           'name'       => Like::class,
           'email'      => Like::class,
           'updated_at' => WhereDateStartEnd::class,
           'created_at' => WhereDateStartEnd::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'role',
        'updated_at',
        'created_at',
    ];

    protected $appends = ['bonus_balance'];

    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function bonusTransactions()
    {
        return $this->hasMany(BonusTransaction::class);
    }

    public function getBonusBalanceAttribute()
    {
        return $this->bonusTransactions()->sum('amount');
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->with('products')->orderBy('created_at', 'desc');
    }

    public function cart()
    {
        return $this->hasOne(Order::class)->where('status', 'created');
    }


    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function mainCompany()
    {
        return $this->hasOne(Company::class)->where('is_main', true);
    }
}
