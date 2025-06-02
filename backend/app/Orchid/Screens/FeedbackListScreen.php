<?php

namespace App\Orchid\Screens;

use App\Models\Feedback;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;

class FeedbackListScreen extends Screen
{
    public function name(): ?string
    {
        return 'Обратная связь';
    }

    public function description(): ?string
    {
        return 'Список всех обращений от пользователей';
    }

    public function query(): array
    {
        return [
            'feedbacks' => Feedback::with('user')
                ->defaultSort('created_at', 'desc')
                ->paginate(),
        ];
    }

    public function commandBar(): array
    {
        return [];
    }

    public function layout(): array
    {
        return [
            Layout::table('feedbacks', [
                TD::make('user.name', 'Пользователь')
                    ->render(function (Feedback $feedback) {
                        return $feedback->user ? $feedback->user->name : 'Гость';
                    }),
                
                TD::make('subject', 'Тема'),
                
                TD::make('message', 'Сообщение')
                    ->width('300px')
                    ->render(function (Feedback $feedback) {
                        return \Illuminate\Support\Str::limit($feedback->message, 100);
                    }),
                
                TD::make('status', 'Статус')
                    ->render(function (Feedback $feedback) {
                        return Feedback::statuses()[$feedback->status] ?? $feedback->status;
                    })
                    ->sort(),
                
                TD::make('created_at', 'Дата создания')
                    ->sort()
                    ->render(function (Feedback $feedback) {
                        return $feedback->created_at->format('d.m.Y H:i');
                    }),
                
                TD::make('Действия')
                    ->alignRight()
                    ->render(function (Feedback $feedback) {
                        return Link::make('Просмотр')
                            ->route('platform.feedback.edit', $feedback);
                    }),
            ]),
        ];
    }
}