<?php

namespace App\Orchid\Screens;

use App\Models\Application;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Color;

class ApplicationListScreen extends Screen
{
    public $name = 'Заявки';
    public $description = 'Список всех заявок';

    public function query(): array
    {
        return [
            'applications' => Application::with('user')
                ->filters()
                ->defaultSort('created_at', 'desc')
                ->paginate()
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Создать заявку')
                ->icon('plus')
                ->route('platform.application.create')
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('applications', [
                TD::make('id', 'ID')
                    ->sort()
                    ->filter(TD::FILTER_TEXT),

                TD::make('user.name', 'Пользователь')
                    ->sort(),

                TD::make('title', 'Название')
                    ->sort()
                    ->filter(TD::FILTER_TEXT),

                TD::make('status', 'Статус')
                    ->sort()
                    ->render(function (Application $application) {
                        return $this->getStatusBadge($application->status);
                    }),

                TD::make('amount', 'Сумма')
                    ->sort()
                    ->render(function (Application $application) {
                        return number_format($application->amount, 2) . ' ₽';
                    }),

                TD::make('created_at', 'Создано')
                    ->sort()
                    ->render(function (Application $application) {
                        return $application->created_at->format('d.m.Y H:i');
                    }),

                TD::make('Действия')
                    ->alignRight()
                    ->render(function (Application $application) {
                        return Link::make('Редактировать')
                            ->icon('pencil')
                            ->route('platform.application.edit', $application);
                    }),
            ])
        ];
    }

    protected function getStatusBadge(string $status): string
    {
        $statusLabels = [
            'pending'    => 'Ожидание',
            'processing' => 'В обработке',
            'completed'  => 'Завершено',
            'cancelled'  => 'Отменено',
        ];

        $colors = [
            'pending'    => Color::WARNING()->name(),
            'processing' => Color::INFO()->name(),
            'completed'  => Color::SUCCESS()->name(),
            'cancelled'  => Color::DANGER()->name(),
        ];

        $label = $statusLabels[strtolower($status)] ?? ucfirst($status);
        $color = $colors[strtolower($status)] ?? Color::SECONDARY()->name();
        
        return "<span class='badge bg-{$color}'>{$label}</span>";
    }
}