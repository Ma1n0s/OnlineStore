<?php

namespace App\Orchid\Screens;

use App\Models\BonusCard;
use App\Models\User;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;

class BonusCardListScreen extends Screen
{
    public $name = 'Бонусные карты';
    public $description = 'Список всех бонусных карт';

    public function query(): array
    {
        return [
            'bonus_cards' => BonusCard::with('user')
                ->filters()
                ->defaultSort('created_at', 'desc')
                ->paginate()
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Создать бонусную карту')
                ->icon('plus')
                ->route('platform.bonus-cards.create')
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('bonus_cards', [
                TD::make('card_number', 'Номер карты')
                    ->sort()
                    ->filter(TD::FILTER_TEXT),

                TD::make('user.name', 'Владелец')
                    ->sort()
                    ->render(function (BonusCard $card) {
                        return $card->user->name ?? '-';
                    }),

                TD::make('current_level', 'Текущий уровень')
                    ->sort(),

                TD::make('max_level', 'Макс. уровень')
                    ->sort(),

                TD::make('points', 'Баллы')
                    ->sort(),

                TD::make('points_to_next_level', 'Баллов до след. уровня')
                    ->sort(),

                TD::make('Действия')
                    ->alignRight()
                    ->render(function (BonusCard $card) {
                        return Link::make('Редактировать')
                            ->icon('pencil')
                            ->route('platform.bonus-cards.edit', $card);
                    }),
            ])
        ];
    }
}