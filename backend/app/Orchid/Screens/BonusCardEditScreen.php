<?php

namespace App\Orchid\Screens;

use App\Models\BonusCard;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class BonusCardEditScreen extends Screen
{
    public $name = 'Редактирование бонусной карты';
    public $description = 'Управление параметрами бонусной карты';

    protected $bonusCard;

    public function query(BonusCard $bonusCard): array
    {
        $this->bonusCard = $bonusCard;
        
        return [
            'bonus_card' => $bonusCard
        ];
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
                ->canSee($this->bonusCard->exists),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Select::make('bonus_card.user_id')
                    ->title('Пользователь')
                    ->required()
                    ->fromModel(User::class, 'name')
                    ->empty('Не выбрано'),
                    
                Input::make('bonus_card.card_number')
                    ->title('Номер карты')
                    ->required(),
                    
                Input::make('bonus_card.current_level')
                    ->title('Текущий уровень')
                    ->type('number')
                    ->required(),
                    
                Input::make('bonus_card.max_level')
                    ->title('Максимальный уровень')
                    ->type('number')
                    ->required(),
                    
                Input::make('bonus_card.points')
                    ->title('Баллы')
                    ->type('number')
                    ->required(),
                    
                Input::make('bonus_card.points_to_next_level')
                    ->title('Баллов до следующего уровня')
                    ->type('number')
                    ->required(),
            ])
        ];
    }

    public function save(BonusCard $bonusCard, Request $request)
    {
        $request->validate([
            'bonus_card.user_id' => 'required|exists:users,id',
            'bonus_card.card_number' => 'required|unique:bonus_cards,card_number,'.$bonusCard->id,
            'bonus_card.current_level' => 'required|numeric|min:1',
            'bonus_card.max_level' => 'required|numeric|min:1|gte:bonus_card.current_level',
            'bonus_card.points' => 'required|numeric|min:0',
            'bonus_card.points_to_next_level' => 'required|numeric|min:1',
        ]);

        $bonusCard->fill($request->get('bonus_card'))->save();

        Alert::success('Бонусная карта успешно сохранена.');
    }

    public function remove(BonusCard $bonusCard)
    {
        $bonusCard->delete();
        Alert::info('Бонусная карта удалена.');
        
        return redirect()->route('platform.bonus-cards.list');
    }
}