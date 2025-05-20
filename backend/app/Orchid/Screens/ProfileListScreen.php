<?php

namespace App\Orchid\Screens;

use App\Models\User;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;

class ProfileListScreen extends Screen
{
    public $name = 'Профили пользователей';
    public $description = 'Список всех профилей';

    public function query(): array
    {
        return [
            'users' => User::with('profile')
                ->filters()
                ->defaultSort('created_at', 'desc')
                ->paginate()
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Добавить пользователя')
                ->icon('plus')
                ->route('platform.systems.users.create')
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('users', [
                TD::make('id', 'ID')
                    ->sort()
                    ->filter(TD::FILTER_TEXT),

                TD::make('name', 'Имя')
                    ->sort()
                    ->filter(TD::FILTER_TEXT),
                    
                TD::make('profile.last_name', 'Фамилия')
                    ->sort()
                    ->render(function (User $user) {
                        return $user->profile->last_name ?? '-';
                    }),

                TD::make('email', 'Email')
                    ->sort()
                    ->filter(TD::FILTER_TEXT),

                TD::make('phone', 'Телефон')
                    ->sort()
                    ->render(function (User $user) {
                        return $user->phone ?? '-';
                    }),

                TD::make('profile.company_name', 'Компания')
                    ->sort()
                    ->render(function (User $user) {
                        return $user->profile->company_name ?? '-';
                    }),

                TD::make('role', 'Роль')
                    ->sort()
                    ->render(function (User $user) {
                        return $user->role === 'admin' ? 'Администратор' : 'Пользователь';
                    }),

                TD::make('Действия')
                    ->alignRight()
                    ->render(function (User $user) {
                        return Link::make('Редактировать')
                            ->icon('pencil')
                            ->route('platform.profiles.edit', $user);
                    }),
            ])
        ];
    }
}