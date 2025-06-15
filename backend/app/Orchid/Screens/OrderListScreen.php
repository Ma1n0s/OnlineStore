<?php

namespace App\Orchid\Screens;

use App\Models\Order;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Color;

class OrderListScreen extends Screen
{
    public $name = 'Заказы';
    public $description = 'Список всех заказов';

    public function query(): array
    {
        return [
            'orders' => Order::with(['user', 'products'])
                ->where('status', '!=', 'created')
                ->filters()
                ->defaultSort('created_at', 'desc')
                ->paginate(),
        ];
    }
    

    public function commandBar(): array
    {
        return [
            Link::make('Создать заказ')
                ->icon('plus')
                ->route('platform.order.create')
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('orders', [
                TD::make('id', 'ID')
                    ->sort()
                    ->filter(TD::FILTER_TEXT),

                TD::make('user.name', 'Пользователь')
                    ->sort(),

                TD::make('order_number', 'Номер заказа')
                    ->sort(),

                TD::make('products_count', 'Товары')
                    ->render(function (Order $order) {
                        return $order->products->count();
                    }),

                TD::make('total_amount', 'Сумма')
                    ->sort()
                    ->align(TD::ALIGN_RIGHT)
                    ->render(function (Order $order) {
                        return '₽' . number_format($order->total_amount, 2);
                    }),

                TD::make('status', 'Статус')
                    ->sort()
                    ->render(function (Order $order) {
                        return $this->getStatusBadge($order->status);
                    }),

                TD::make('created_at', 'Создано')
                    ->sort()
                    ->render(function (Order $order) {
                        return $order->created_at->format('d.m.Y H:i');
                    }),

                TD::make('Действия')
                    ->alignRight()
                    ->render(function (Order $order) {
                        return Link::make('Редактировать')
                            ->icon('pencil')
                            ->route('platform.order.edit', $order);
                    }),
            ])
        ];
    }

    protected function getStatusBadge(string $status): string
    {
        $statusLabels = [
            'pending'    => 'Ожидание',
            'processing' => 'В обработке',
            'completed'  => 'Завершено',
            'cancelled'  => 'Отменено',
        ];

        $colors = [
            'pending'    => Color::WARNING()->name(),
            'processing' => Color::INFO()->name(),
            'completed'  => Color::SUCCESS()->name(),
            'cancelled'  => Color::DANGER()->name(),
        ];

        $label = $statusLabels[strtolower($status)] ?? ucfirst($status);
        $color = $colors[strtolower($status)] ?? Color::SECONDARY()->name();
        
        return "<span class='badge bg-{$color}'>{$label}</span>";
    }
}