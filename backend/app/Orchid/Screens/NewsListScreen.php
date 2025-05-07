<?php

namespace App\Orchid\Screens;

use App\Models\News;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class NewsListScreen extends Screen
{
    public function query(): array
    {
        return [
            'news' => News::orderBy('sort_order')->get()
        ];
    }

    public function name(): ?string
    {
        return 'Новости';
    }

    public function commandBar(): array
    {
        return [
            Link::make('Добавить')
                ->icon('plus')
                ->route('platform.news.create'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('News', [
                TD::make('title', 'Заголовок'),
                TD::make('icon', 'Иконка'),
                TD::make('is_special', 'Специальный')->render(fn($a) => $a->is_special ? 'Да' : 'Нет'),
                TD::make('sort_order', 'Порядок'),
                TD::make('actions', 'Действия')->render(function (News $News) {
                    return Link::make('Редактировать')
                        ->route('platform.news.edit', $News);
                }),
            ])
        ];
    }
}