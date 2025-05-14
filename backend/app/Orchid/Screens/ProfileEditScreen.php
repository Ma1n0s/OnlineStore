<?php

namespace App\Orchid\Screens;

use App\Models\User;
use App\Models\BonusCard;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class ProfileEditScreen extends Screen
{
    public $name = 'Редактирование профиля';
    public $description = 'Полная информация о пользователе и компании';

    protected $user;

    public function query(User $user): array
    {
        $this->user = $user;
        
        return [
            'user' => $user->load(['profile', 'bonusCard']),
            'profile' => $user->profile ?? $user->profile()->create(),
            'bonus_card' => $user->bonusCard
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save'),
                
            Button::make('Удалить профиль')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->user->profile !== null),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::tabs([
                'Личная информация' => Layout::rows([
                    Input::make('user.name')
                        ->title('Имя')
                        ->required(),
                        
                    Input::make('profile.last_name')
                        ->title('Фамилия'),
                        
                    Input::make('profile.patronymic')
                        ->title('Отчество'),
                        
                    Input::make('user.email')
                        ->title('Email')
                        ->required()
                        ->type('email'),
                        
                    Input::make('user.phone')
                        ->title('Телефон')
                        ->mask('+7 (999) 999-99-99'),
                ]),
                
                'Компания' => Layout::rows([
                    Input::make('profile.company_name')
                        ->title('Название компании'),
                        
                    Input::make('profile.inn')
                        ->title('ИНН')
                        ->mask('9999999999'),
                        
                    Input::make('profile.kpp')
                        ->title('КПП')
                        ->mask('999999999'),
                        
                    TextArea::make('profile.legal_address')
                        ->title('Юридический адрес')
                        ->rows(3),
                ]),

                'Бонусная карта' => Layout::rows([
                    Input::make('bonus_card.card_number')
                        ->title('Номер карты')
                        ->disabled()
                        ->canSee($this->user->bonusCard !== null),
                        
                    // Input::make('bonus_card.current_level')
                    //     ->title('Текущий уровень')
                    //     ->type('number')
                    //     ->canSee($this->user->bonusCard !== null),
                        
                    Input::make('bonus_card.points')
                        ->title('Баллы')
                        ->type('number')
                        ->canSee($this->user->bonusCard !== null),
                        
                    // Input::make('bonus_card.points_to_next_level')
                    //     ->title('Баллов до следующего уровня')
                    //     ->type('number')
                    //     ->canSee($this->user->bonusCard !== null),
                        
                    Button::make('Создать бонусную карту')
                        ->method('createBonusCard')
                        ->canSee($this->user->bonusCard === null),
                ]),
            ])
        ];
    }

    public function save(User $user, Request $request)
    {
        $request->validate([
            'user.name' => 'required',
            'user.email' => 'required|email',
            'profile.inn' => 'nullable|digits:10',
            'profile.kpp' => 'nullable|digits:9',
        ]);

        $userData = $request->input('user');
        
        if (isset($userData['phone'])) {
            $phoneDigits = preg_replace('/[^0-9]/', '', $userData['phone']);
            
            if (strlen($phoneDigits) === 11 && in_array($phoneDigits[0], ['7'])) {
                $userData['phone'] = '+' . $phoneDigits[0] . ' (' . substr($phoneDigits, 1, 3) . ') ' . substr($phoneDigits, 4, 3) . '-' . substr($phoneDigits, 7, 2) . '-' . substr($phoneDigits, 9, 2);
            }
            else {
                $userData['phone'] = $userData['phone'];
            }
        }

        $user->update($userData);
        $user->profile()->updateOrCreate([], $request->input('profile'));

        if ($user->bonusCard && $request->has('bonus_card')) {
            $user->bonusCard->update($request->input('bonus_card'));
        }

        Alert::success('Данные успешно сохранены.');
    }

    public function remove(User $user)
    {
        $user->profile()->delete();
        Alert::info('Профиль удален.');
        
        return redirect()->route('platform.profiles.list');
    }

    public function createBonusCard(User $user)
    {
        $bonusCard = new BonusCard();
        $bonusCard->user_id = $user->id;
        $bonusCard->card_number = $bonusCard->generateCardNumber();
        $bonusCard->max_level = 5;
        $bonusCard->current_level = 1;
        $bonusCard->points = 0;
        $bonusCard->points_to_next_level = 100;
        $bonusCard->save();

        Alert::success('Бонусная карта создана.');
        return back();
    }
}