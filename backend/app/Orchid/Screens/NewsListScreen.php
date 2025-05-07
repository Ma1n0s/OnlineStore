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
            'news' => News::latest()->get()
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
            Layout::table('news', [
                TD::make('title', 'Заголовок')->render(function ($news) {
                    return $news->title;
                }),
                TD::make('created_at', 'Дата создания')->render(fn($news) => $news->created_at->format('d.m.Y')),
                TD::make('actions', 'Действия')->render(function (News $news) {
                    return Link::make('Редактировать')
                        ->route('platform.news.edit', $news);
                }),
            ])
        ];
    }
}