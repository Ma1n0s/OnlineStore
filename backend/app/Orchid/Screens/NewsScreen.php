<?php

namespace App\Orchid\Screens;

use App\Models\News;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class NewsScreen extends Screen
{
    public $News;

    public function query(News $News): array
    {
        return [
            'news' => $News
        ];
    }

    public function name(): ?string
    {
        return $this->News->exists ? 'Редактирование новости' : 'Создание новости';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save'),

            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->News->exists),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('News.title')
                    ->title('Заголовок')
                    ->required(),

                Input::make('News.icon')
                    ->title('Иконка (Material Symbols)')
                    ->help('Например: material-symbols:check-circle-outline-rounded'),

                TextArea::make('News.description')
                    ->title('Описание'),

                Input::make('News.link')
                    ->title('Ссылка'),

                CheckBox::make('News.is_special')
                    ->title('Специальный блок')
                    ->sendTrueOrFalse(),

                Input::make('News.sort_order')
                    ->title('Порядок сортировки')
                    ->type('number'),
            ])
        ];
    }

    public function save(News $News, Request $request)
    {
        $data = $request->validate([
            'News.title' => 'required|string|max:255',
            'News.icon' => 'nullable|string|max:255',
            'News.description' => 'nullable|string',
            'News.link' => 'nullable|string|max:255',
            'News.is_special' => 'boolean',
            'News.sort_order' => 'integer'
        ]);

        $News->fill($data['News'])->save();

        Alert::success('Новость успешно сохранена.');

        return redirect()->route('platform.news.list');
    }

    public function remove(News $News)
    {
        $News->delete();
        Alert::success('Новость успешно удалена.');
        return redirect()->route('platform.news.list');
    }
}