<?php

namespace App\Orchid\Screens;

use App\Models\Application;
use App\Models\User;
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

    public function query(Application $application): array
    {
        $this->exists = $application->exists;

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
        return [
            Layout::rows([
                Relation::make('application.user_id')
                    ->title('Пользователь')
                    ->required()
                    ->fromModel(User::class, 'name'),

                Input::make('application.title')
                    ->title('Название')
                    ->required()
                    ->placeholder('Название заявки'),

                TextArea::make('application.description')
                    ->title('Описание')
                    ->rows(3)
                    ->placeholder('Описание заявки'),

                Select::make('application.status')
                    ->title('Статус')
                    ->options([
                        'pending' => 'Ожидание',
                        'processing' => 'В обработке',
                        'completed' => 'Завершено',
                        'cancelled' => 'Отменено',
                    ])
                    ->required(),

                Input::make('application.amount')
                    ->title('Сумма')
                    ->type('number')
                    ->step(0.01)
                    ->required(),
            ])
        ];
    }

    public function createOrUpdate(Application $application, Request $request)
    {
        $data = $request->get('application');
        
        $application->fill($data)->save();

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