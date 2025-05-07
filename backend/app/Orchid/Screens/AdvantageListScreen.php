<?php

namespace App\Orchid\Screens;

use App\Models\Advantage;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class AdvantageListScreen extends Screen
{
    public function query(): array
    {
        return [
            'advantages' => Advantage::orderBy('sort_order')->get()
        ];
    }

    public function name(): ?string
    {
        return 'Преимущества';
    }

    public function commandBar(): array
    {
        return [
            Link::make('Добавить')
                ->icon('plus')
                ->route('platform.advantages.create'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('advantages', [
                TD::make('title', 'Заголовок'),
                TD::make('icon', 'Иконка'),
                TD::make('is_special', 'Специальный')->render(fn($a) => $a->is_special ? 'Да' : 'Нет'),
                TD::make('sort_order', 'Порядок'),
                TD::make('actions', 'Действия')->render(function (Advantage $advantage) {
                    return Link::make('Редактировать')
                        ->route('platform.advantages.edit', $advantage);
                }),
            ])
        ];
    }
}