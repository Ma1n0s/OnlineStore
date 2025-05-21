<?php

namespace App\Orchid\Screens;

use App\Models\User;
use App\Models\BonusCard;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
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
            'bonus_card' => $user->bonusCard,
            'bonus_transactions' => $user->bonusTransactions()->latest()->get(),
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
                      
                Input::make('user.email')
                    ->title('Email')
                    ->required()
                    ->type('email'),
                    
                Input::make('user.phone')
                    ->title('Телефон')
                    ->mask('+7 (999) 999-99-99'),

                Select::make('user.role')
                    ->title('Роль')
                    ->options([
                        'user' => 'Пользователь',
                        'admin' => 'Администратор',
                    ]),
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

'Бонусы' => [
                Layout::rows([
                    Input::make('bonus_balance')
                        ->title('Текущий баланс бонусов')
                        ->value($this->user->bonusTransactions()->sum('amount'))
                        ->readonly(),
                ]),
                
                // Форма для добавления новой операции
                Layout::rows([
                    Select::make('operation')
                        ->title('Тип операции')
                        ->options([
                            'Начисление бонусов' => 'Начисление',
                            'Списание бонусов' => 'Списание',
                        ])
                        ->required(),
                        
                    Input::make('amount')
                        ->title('Сумма')
                        ->type('number'),
                        
                    Button::make('Добавить операцию')
                        ->method('addBonusTransaction')
                        ->icon('plus')
                        ->class('btn btn-primary'),
                ]),
                
                Layout::table('bonus_transactions', [
                    TD::make('date', 'Дата')
                        ->sort()
                        ->render(function ($transaction) {
                            return $transaction->date->format('d.m.Y');
                        }),
                        
                    TD::make('operation', 'Операция'),
                    
                    TD::make('amount', 'Сумма')
                        ->render(function ($transaction) {
                            return ($transaction->amount > 0 ? '+' : '') . $transaction->amount;
                        }),
                    
                    TD::make('status', 'Статус'),
                    
                ]),
            ],     
        ])
    ];
}

    public function addBonusTransaction(User $user, Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'operation' => 'required|in:Начисление бонусов,Списание бонусов',
        ]);
        
        $user->bonusTransactions()->create([
            'date' => now(),
            'operation' => $request->operation,
            'amount' => $request->operation === 'Начисление бонусов' 
                ? abs($request->amount) 
                : -abs($request->amount),
            'status' => 'Завершено',
        ]);
        
        Alert::success('Бонусная операция успешно добавлена');
        
        return back();
    }

    public function save(User $user, Request $request)
    {
        $request->validate([
            'user.name' => 'required',
            'user.email' => 'required|email',
            'user.role' => 'required|in:user,admin',
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
}