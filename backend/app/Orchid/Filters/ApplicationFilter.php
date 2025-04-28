<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class ApplicationFilter extends Filter
{
    public function parameters(): array
    {
        return ['title', 'status'];
    }

    public function name(): string
    {
        return 'Фильтр заявок';
    }

    public function run(Builder $builder): Builder
    {
        if ($this->request->get('title')) {
            $builder->where('title', 'LIKE', "%{$this->request->get('title')}%");
        }

        if ($this->request->get('status')) {
            $builder->where('status', $this->request->get('status'));
        }

        return $builder;
    }

    public function display(): array
    {
        return [
            Input::make('title')
                ->title('Название')
                ->placeholder('Поиск по названию'),

            Select::make('status')
                ->title('Статус')
                ->options([
                    'pending' => 'Ожидание',
                    'processing' => 'В обработке',
                    'completed' => 'Завершено',
                    'cancelled' => 'Отменено',
                ]),
        ];
    }
}