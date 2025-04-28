<?php

namespace App\Orchid\Screens;

use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class UserProfileScreen extends Screen
{
    public $name = 'Профиль пользователя';
    public $description = 'Редактирование личных данных и информации о компании';

    public function query(User $user): array
    {
        return [
            'user' => $user->load('profile'),
            'profile' => $user->profile ?? $user->profile()->create()
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

        $user->update($request->input('user'));
        $user->profile()->updateOrCreate([], $request->input('profile'));

        Alert::success('Данные успешно сохранены.');
    }

    public function remove(User $user)
    {
        $user->profile()->delete();
        Alert::info('Профиль удален.');
        
        return redirect()->route('platform.systems.users');
    }
}
