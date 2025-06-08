<?php

namespace App\Orchid\Screens;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;

class OrderEditScreen extends Screen
{
    public $name = 'Редактирование заказа';
    public $description = 'Создание или редактирование заказа';

    public $exists = false;
    public $order;

    public function query(Order $order): array
    {
        $this->exists = $order->exists;
        $this->order = $order; 

        if ($this->exists) {
            $this->name = 'Редактировать заказ';
        }

        return [
            'order' => $order->load(['user', 'products'])
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make('Создать')
                ->icon('plus')
                ->method('createOrUpdate')
                ->canSee(!$this->exists),

            Button::make('Обновить')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->exists),

            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->exists),
        ];
    }

    public function layout(): array
    {
        $statusOptions = [
            'pending' => 'Ожидание',
            'processing' => 'В обработке',
            'completed' => 'Завершено',
            'cancelled' => 'Отменено',
        ];

        return [
            Layout::rows([
                Relation::make('order.user_id')
                    ->title('Пользователь')
                    ->required()
                    ->fromModel(User::class, 'name'),

                Relation::make('order.products.')
                    ->title('Товары')
                    ->fromModel(Product::class, 'name')
                    ->multiple()
                    ->displayAppend('full_name')
                    ->applyScope('available'),

                Select::make('order.status')
                    ->title('Статус')
                    ->options($statusOptions)
                    ->required()
                    ->disabled($this->exists && $this->order->status === 'completed'),

                Input::make('order.total_amount')
                    ->title('Сумма заказа')
                    ->type('number')
                    ->step('0.01')
                    ->required(),

                Input::make('order.weight')
                    ->title('Вес (кг)')
                    ->type('number')
                    ->step('0.01')
                    ->required(),

                Input::make('order.order_number')
                    ->title('Номер заказа')
                    ->required(),
            ]),

            Layout::table('order.products', [
                TD::make('name', 'Название')
                    ->render(function (Product $product) {
                        return $product->name;
                    }),

                TD::make('quantity', 'Количество')
                    ->render(function (Product $product) {
                        return $product->pivot->quantity;
                    }),

                TD::make('price_at_order', 'Стоимость')
                    ->align(TD::ALIGN_RIGHT)
                    ->render(function (Product $product) {
                        return '₽' . number_format($product->pivot->price_at_order, 2);
                    }),

                TD::make('total', 'Общая стоимость')
                    ->align(TD::ALIGN_RIGHT)
                    ->render(function (Product $product) {
                        $total = $product->pivot->quantity * $product->pivot->price_at_order;
                        return '₽' . number_format($total, 2);
                    }),

                TD::make('type', 'Тип товара')
                    ->render(function (Product $product) {
                        $types = [
                            'preorder' => 'Под заказ',
                            'rent' => 'В аренду',
                            'instock' => 'В наличии',
                        ];
                        return $types[$product->type] ?? $product->type;
                    }),
            ])->title('Товары в заказе')->canSee($this->exists),
        ];
    }

    public function createOrUpdate(Order $order, Request $request)
    {
        $data = $request->get('order');
        $oldStatus = $order->exists ? $order->status : null;

        // Prevent changing status from 'completed'
        if ($oldStatus === 'completed' && $data['status'] !== 'completed') {
            Alert::error('Статус "Завершено" нельзя изменить.');
            return back();
        }

        $order->fill($data)->save();

        if (isset($data['products'])) {
            $order->products()->sync($data['products']);
        }

        // Award bonuses when status changes to 'completed'
        if ($data['status'] === 'completed' && $oldStatus !== 'completed') {
            $this->awardOrderBonuses($order);
        }

        Alert::info('Заказ успешно сохранен.');
        return redirect()->route('platform.order.list');
    }

    protected function awardOrderBonuses(Order $order)
    {
        $user = $order->user;
        if (!$user) return;

        $totalBonuses = 0;
        
        foreach ($order->products as $product) {
            if ($product->bonuses > 0) {
                $totalBonuses += $product->bonuses * $product->pivot->quantity;
            }
        }

        if ($totalBonuses > 0) {
            $user->bonusTransactions()->create([
                'date' => now(),
                'operation' => 'Начисление бонусов за заказ #' . $order->order_number,
                'amount' => $totalBonuses,
                'status' => 'Завершено',
            ]);
            
            // If you're using the morphMany relationship in Order model
            // $order->bonusTransactions()->create([
            //     'user_id' => $user->id,
            //     'date' => now(),
            //     'operation' => 'Начисление бонусов за заказ #' . $order->order_number,
            //     'amount' => $totalBonuses,
            //     'status' => 'Завершено',
            // ]);
        }
    }

    public function remove(Order $order)
    {
        $order->delete();

        Alert::info('Заказ успешно удален.');

        return redirect()->route('platform.order.list');
    }
}