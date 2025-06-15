<?php

namespace App\Orchid\Screens;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;


class ProfileEditScreen extends Screen
{
    public $name = 'Редактирование профиля';
    public $description = 'Полная информация о пользователе и компании';

    protected $user;

    public function query(User $user): array
    {
        $this->user = $user;
        
        return [
            'user' => $user->load(['profile']),
            'profile' => $user->profile ?? $user->profile()->create(),
            'bonus_transactions' => $user->bonusTransactions()->latest()->get(),
            // 'orders' => $user->orders()->latest()->get(),
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

                'Смена пароля' => Layout::rows([
                    Input::make('current_password')
                        ->title('Текущий пароль')
                        ->type('password'),
                        
                    Input::make('new_password')
                        ->title('Новый пароль')
                        ->type('password'),
                        
                    Input::make('new_password_confirmation')
                        ->title('Подтверждение нового парола')
                        ->type('password'),
                        
                    Button::make('Сменить пароль')
                        ->method('changePassword')
                        ->icon('lock'),
                ]),
                
                'Компании' => [
                    Layout::rows([
                        Button::make('Добавить компанию')
                            ->icon('plus')
                            ->method('addCompany')
                            ->canSee($this->user->companies->count() < 3),
                    ]),
                    
                    ...$this->user->companies->map(function ($company, $index) {
                        return Layout::rows([
                            Input::make("companies[{$company->id}][name]")
                                ->title('Название компании')
                                ->value($company->name),
                                
                            Input::make("companies[{$company->id}][inn]")
                                ->title('ИНН')
                                ->mask('9999999999')
                                ->value($company->inn),
                                
                            Input::make("companies[{$company->id}][kpp]")
                                ->title('КПП')
                                ->mask('999999999')
                                ->value($company->kpp),
                                
                            TextArea::make("companies[{$company->id}][legal_address]")
                                ->title('Юридический адрес')
                                ->rows(3)
                                ->value($company->legal_address),
                                
                            Input::make("companies[{$company->id}][director]")
                                ->title('Директор')
                                ->value($company->director),
                                
                            Input::make("companies[{$company->id}][phone]")
                                ->title('Телефон компании')
                                ->value($company->phone),
                                
                            Input::make("companies[{$company->id}][email]")
                                ->title('Email компании')
                                ->type('email')
                                ->value($company->email),
                                
                            CheckBox::make("companies[{$company->id}][is_main]")
                                ->title('Основная компания')
                                ->value($company->is_main)
                                ->sendTrueOrFalse(),
                                
                            Button::make('Удалить компанию')
                                ->method('removeCompany')
                                ->parameters(['id' => $company->id])
                                ->icon('trash')
                                ->confirm('Вы уверены, что хотите удалить эту компанию?')
                                ->canSee($this->user->companies->count() > 1),
                        ])->title($company->name ?: "Компания #" . ($index + 1));
                    })->toArray()
                ],

                'Бонусы' => [
                    Layout::rows([
                        Input::make('bonus_balance')
                            ->title('Текущий баланс бонусов')
                            ->value($this->user->bonusTransactions()->sum('amount'))
                            ->readonly(),
                    ]),
                
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
                
                // 'Заказы' => [
                //     Layout::table('orders', [
                //         TD::make('order_number', 'Номер заказа')
                //             ->sort()
                //             ->filter(TD::FILTER_TEXT),
                            
                //         TD::make('created_at', 'Дата')
                //             ->sort()
                //             ->render(function ($order) {
                //                 return $order->created_at->format('d.m.Y H:i');
                //             }),
                            
                //         TD::make('total_amount', 'Сумма')
                //             ->sort()
                //             ->render(function ($order) {
                //                 return number_format($order->total_amount, 2, '.', ' ') . ' ₽';
                //             }),
                            
                //         TD::make('status', 'Статус')
                //             ->sort()
                //             ->render(function ($order) {
                //                 return Select::make("orders.{$order->id}.status")
                //                     ->options([
                //                         'pending' => 'В обработке',
                //                         'processing' => 'В процессе',
                //                         'completed' => 'Завершен',
                //                         'cancelled' => 'Отменен'
                //                     ])
                //                     ->value($order->status);
                //             }),
                            
                //         TD::make('is_paid', 'Оплата')
                //             ->sort()
                //             ->render(function ($order) {
                //                 return CheckBox::make("orders.{$order->id}.is_paid")
                //                     ->value($order->is_paid)
                //                     ->sendTrueOrFalse();
                //             }),
                            
                //         TD::make('Действия')
                //             ->render(function ($order) {
                //                 return DropDown::make()
                //                     ->icon('options-vertical')
                //                     ->list([
                //                         Link::make('Просмотр')
                //                             ->route('platform.orders.view', $order->id)
                //                             ->icon('eye'),
                                            
                //                         Button::make('Удалить')
                //                             ->method('removeOrder')
                //                             ->parameters(['id' => $order->id])
                //                             ->icon('trash')
                //                             ->confirm('Вы уверены что хотите удалить этот заказ?'),
                //                     ]);
                //             }),
                //     ]),
                    
                //     // Layout::rows([
                //     //     Button::make('Сохранить изменения')
                //     //         ->method('saveOrders')
                //     //         ->icon('check'),
                //     // ]),
                // ],
            ])
        ];
    }

    public function changePassword(User $user, Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed|different:current_password',
        ]);

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        Alert::success('Пароль успешно изменен');
        return back();
    }

    public function addCompany(User $user)
    {
        if ($user->companies->count() >= 3) {
            Alert::error('Максимальное количество компаний - 3');
            return back();
        }
        
        $user->companies()->create([
            'is_main' => $user->companies->isEmpty()
        ]);
        
        Alert::info('Новая компания добавлена');
        return back();
    }

    public function removeCompany(User $user, Request $request)
    {
        $company = $user->companies()->find($request->input('id'));
        
        if ($company) {
            $company->delete();
            
            if ($company->is_main && $user->companies->count() > 0) {
                $user->companies()->first()->update(['is_main' => true]);
            }
            
            Alert::info('Компания удалена');
        }
        
        return back();
    }


    public function saveOrders(User $user, Request $request)
    {
        $request->validate([
            'orders' => 'sometimes|array',
            'orders.*.status' => 'sometimes|in:pending,processing,completed,cancelled',
            'orders.*.is_paid' => 'sometimes|boolean',
        ]);
        
        foreach ($request->input('orders', []) as $id => $data) {
            $order = $user->orders()->find($id);
            
            if ($order) {
                $order->update([
                    'status' => $data['status'] ?? $order->status,
                    'is_paid' => $data['is_paid'] ?? $order->is_paid,
                ]);
            }
        }
        
        Alert::success('Изменения в заказах сохранены.');
    }

    public function removeOrder(User $user, Request $request)
    {
        $order = $user->orders()->find($request->input('id'));
        
        if ($order) {
            $order->delete();
            Alert::info('Заказ удален.');
        }
        
        return back();
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
            'companies.*.inn' => 'nullable|digits:10',
            'companies.*.kpp' => 'nullable|digits:9',
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
        
        foreach ($request->input('companies', []) as $id => $companyData) {
            $company = $user->companies()->find($id);
            if ($company) {
                $company->update($companyData);
            }
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