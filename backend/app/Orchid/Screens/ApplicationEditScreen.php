<?php

namespace App\Orchid\Screens;

use App\Models\Application;
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

class ApplicationEditScreen extends Screen
{
    public $name = 'Редактирование заявки';
    public $description = 'Создание или редактирование заявки';

    public $exists = false;
    public $application;

    public function query(Application $application): array
    {
        $this->exists = $application->exists;
        $this->application = $application; 

        if ($this->exists) {
            $this->name = 'Редактировать заявку';
        }

        return [
            'application' => $application
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
            'cancelled' => 'Отменено',
        ];

        if ($this->exists) {
            $statusOptions['completed'] = 'Завершено';
        }

        return [
            Layout::rows([
                Relation::make('application.user_id')
                    ->title('Пользователь')
                    ->required()
                    ->fromModel(User::class, 'name'),

                Relation::make('application.product_id')
                    ->title('Товар')
                    ->fromModel(Product::class, 'name')
                    ->displayAppend('full_name')
                    ->applyScope('available'),

                Select::make('application.status')
                    ->title('Статус')
                    ->options($statusOptions)
                    ->required()
                    ->disabled($this->exists && $this->application->status === 'completed'),
            ])
        ];
    }

    public function createOrUpdate(Application $application, Request $request)
    {
        $data = $request->get('application');

        $product = Product::find($data['product_id']);
        if (!$product) {
            Alert::error('Товар не найден.');
            return back();
        }

        if (!$application->exists) {
            if ($product->quantity <= 0) {
                Alert::error('Товара нет в наличии, заявку создать нельзя.');
                return back();
            }
        }

        $oldStatus = $application->exists ? $application->status : null;

        if ($oldStatus === 'completed' && $data['status'] !== 'completed') {
            Alert::error('Статус "Завершено" нельзя изменить.');
            return back();
        }

        $application->fill($data)->save();

        if ($data['status'] === 'completed' && $oldStatus !== 'completed') {
            if ($product->quantity > 0) {
                $product->decrement('quantity');
            }

            $user = $application->user;
            if ($user && $product->bonus_points ?? 0 > 0) {
                $user->bonusTransactions()->create([
                    'date' => now(),
                    'operation' => 'Начисление',
                    'amount' => $product->bonus_points,
                    'status' => 'Завершено',
                ]);
            }
        }

        Alert::info('Заявка успешно сохранена.');
        return redirect()->route('platform.application.list');
    }

    public function remove(Application $application)
    {
        $application->delete();

        Alert::info('Заявка успешно удалена.');

        return redirect()->route('platform.application.list');
    }
}