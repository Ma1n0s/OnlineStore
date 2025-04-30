<?php

namespace App\Orchid\Screens;

use App\Models\CartItem;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class CartItemScreen extends Screen
{
    public $name = 'Корзина';
    public $description = 'Просмотр элементов корзины';

    public function query(): array
    {
        return [
            'cartItems' => CartItem::with(['user', 'product'])->filters()->paginate()
        ];
    }

    public function commandBar(): array
    {
        return [];
    }

    public function layout(): array
    {
        return [
            Layout::table('cartItems', [
                TD::make('id', 'ID'),
                TD::make('user.name', 'Пользователь'),
                TD::make('product.name', 'Товар'),
                TD::make('quantity', 'Количество'),
                TD::make('created_at', 'Добавлено'),
            ])
        ];
    }
}