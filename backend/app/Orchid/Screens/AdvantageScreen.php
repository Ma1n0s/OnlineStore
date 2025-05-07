<?php

namespace App\Orchid\Screens;

use App\Models\Advantage;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class AdvantageScreen extends Screen
{
    public $advantage;

    public function query(Advantage $advantage): array
    {
        return [
            'advantage' => $advantage
        ];
    }

    public function name(): ?string
    {
        return $this->advantage->exists ? 'Редактирование преимущества' : 'Создание преимущества';
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
                ->canSee($this->advantage->exists),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('advantage.title')
                    ->title('Заголовок')
                    ->required(),

                Input::make('advantage.icon')
                    ->title('Иконка (Material Symbols)')
                    ->help('Например: material-symbols:check-circle-outline-rounded'),

                TextArea::make('advantage.description')
                    ->title('Описание'),

                Input::make('advantage.link')
                    ->title('Ссылка'),

                CheckBox::make('advantage.is_special')
                    ->title('Специальный блок')
                    ->sendTrueOrFalse(),

                Input::make('advantage.sort_order')
                    ->title('Порядок сортировки')
                    ->type('number'),
            ])
        ];
    }

    public function save(Advantage $advantage, Request $request)
    {
        $data = $request->validate([
            'advantage.title' => 'required|string|max:255',
            'advantage.icon' => 'nullable|string|max:255',
            'advantage.description' => 'nullable|string',
            'advantage.link' => 'nullable|string|max:255',
            'advantage.is_special' => 'boolean',
            'advantage.sort_order' => 'integer'
        ]);

        $advantage->fill($data['advantage'])->save();

        Alert::success('Преимущество успешно сохранено.');

        return redirect()->route('platform.advantages.list');
    }

    public function remove(Advantage $advantage)
    {
        $advantage->delete();
        Alert::success('Преимущество успешно удалено.');
        return redirect()->route('platform.advantages.list');
    }
}