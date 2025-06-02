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
            'applications' => Application::with(['user', 'product.category.parent'])
                ->filters()
                ->defaultSort('created_at', 'desc')
                ->paginate(),
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

                // TD::make('product.name', 'Товар')
                //     ->sort()
                //     ->render(function (Application $application) {
                //         if (!$application->product) {
                //             return '-';
                //         }

                //         $html = '<div class="d-flex flex-column">';
                        
                //         $html .= '<div>' . Link::make($application->product->name)
                //             ->route('platform.product.edit', $application->product) . '</div>';
                        
                //         if ($application->product->category) {
                //             $html .= '<div class="w-100 pt-1" style="font-size: 0.875rem; display: flex; align-items: center;">';
                            
                //             $path = [];
                //             $current = $application->product->category;
                //             while ($current) {
                //                 array_unshift($path, $current);
                //                 $current = $current->parent;
                //             }
                            
                //             $breadcrumbs = [];
                //             foreach ($path as $item) {
                //                 $breadcrumbs[] = Link::make($item->name)
                //                     ->route('platform.category.action', $item)
                //                     ->class('text-decoration-none');
                //             }
                            
                //             $html .= implode(' <span class="mx-1">›</span> ', $breadcrumbs);
                //             $html .= '</div>';
                //         }
                        
                //         $html .= '</div>';
                //         return $html;
                //     }),

                TD::make('quantity', 'Количество')
                    ->sort()
                    ->align(TD::ALIGN_CENTER)
                    ->render(function (Application $application) {
                        if (!$application->product) {
                            return 'товар удален';
                        }
                        
                        $quantity = $application->product->quantity;
                        $class = $quantity <= 0 ? 'text-danger' : '';
                        return "<span class='{$class}'>" . $quantity . "</span>";
                    }),

                TD::make('product.price', 'Цена')
                    ->sort()
                    ->align(TD::ALIGN_RIGHT)
                    ->render(function (Application $application) {
                        return $application->product ? '₽' . number_format((float)$application->product->price, 2) : '-';
                    }),

                TD::make('status', 'Статус')
                    ->sort()
                    ->render(function (Application $application) {
                        return $this->getStatusBadge($application->status);
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