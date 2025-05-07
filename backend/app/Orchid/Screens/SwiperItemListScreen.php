<?php

namespace App\Orchid\Screens;

use App\Models\SwiperItem;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\TD;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SwiperItemListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'swiper_items' => SwiperItem::orderBy('position')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Управление слайдером';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить слайд')
                ->icon('plus')
                ->route('platform.swiper.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('swiper_items', [
                TD::make('id', 'ID')
                    ->sort()
                    ->filter(Input::make())
                    ->render(fn (SwiperItem $swiperItem) => $swiperItem->id),
                    
                TD::make('image', 'Изображение')
                    ->render(function (SwiperItem $swiperItem) {
                        if ($swiperItem->image) {
                            return '<div style="max-width: 200px; max-height: 80px; overflow: hidden;">
                                <img src="'.$swiperItem->image.'" alt="Slide" class="img-fluid" style="width: 100%; object-fit: cover;">
                            </div>';
                        }
                        return 'Нет изображения';
                    }),

                TD::make('link', 'Ссылка')
                    ->sort()
                    ->filter(Input::make())
                    ->render(fn (SwiperItem $swiperItem) => $swiperItem->link ?: 'Без ссылки'),
                    
                TD::make('position', 'Позиция')
                    ->sort()
                    ->filter(Input::make()->type('number'))
                    ->render(fn (SwiperItem $swiperItem) => $swiperItem->position),
                    
                TD::make('created_at', 'Создан')
                    ->sort()
                    ->render(fn (SwiperItem $swiperItem) => $swiperItem->created_at->format('d.m.Y H:i')),
                    
                TD::make('Действия')
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(function (SwiperItem $swiperItem) {
                        return Link::make('Редактировать')
                            ->route('platform.swiper.edit', $swiperItem->id)
                            ->icon('pencil');
                    }),
            ]),
        ];
    }
}
