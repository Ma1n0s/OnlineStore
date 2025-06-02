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
                    ->required(),

                Input::make('order.total_amount')
                    ->title('Сумма заказа')
                    ->type('number')
                    ->step('0.01')
                    ->required(),

                Input::make('order.order_number')
                    ->title('Номер заказа')
                    ->required(),
            ])
        ];
    }

    public function createOrUpdate(Order $order, Request $request)
    {
        $data = $request->get('order');

        $order->fill($data)->save();

        if (isset($data['products'])) {
            $order->products()->sync($data['products']);
        }

        Alert::info('Заказ успешно сохранен.');
        return redirect()->route('platform.order.list');
    }

    public function remove(Order $order)
    {
        $order->delete();

        Alert::info('Заказ успешно удален.');

        return redirect()->route('platform.order.list');
    }
}