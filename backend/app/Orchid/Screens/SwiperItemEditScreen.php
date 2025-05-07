<?php

namespace App\Orchid\Screens;

use App\Models\SwiperItem;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Fields\Upload;

class SwiperItemEditScreen extends Screen
{
    /**
     * @var SwiperItem
     */
    public $swiperItem;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @param SwiperItem $swiperItem
     * @return array
     */
    public function query(SwiperItem $swiperItem): iterable
    {
        $this->swiperItem = $swiperItem;
        
        return [
            'swiper_item' => $swiperItem,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->swiperItem->exists ? 'Редактирование элемента свайпера' : 'Создание элемента свайпера';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('save')
                ->method('save'),
                
            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->swiperItem->exists),
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
            Layout::rows([
                Input::make('swiper_item.link')
                    ->title('Ссылка')
                    ->placeholder('Введите URL-адрес')
                    ->help('Укажите URL, на который будет перенаправлен пользователь при клике'),
                    
                Input::make('swiper_item.position')
                    ->title('Позиция')
                    ->type('number')
                    ->min(0)
                    ->help('Порядок отображения (0 = первый)'),
                    
                Cropper::make('swiper_item.image')
                    ->title('Изображение')
                    ->width(1200)
                    ->height(400)
                    ->targetRelativeUrl()
                    ->value($this->swiperItem->exists ? $this->swiperItem->image : null)
                    ->help('Рекомендуемое соотношение сторон 3:1'),
            ]),
        ];
    }
    
    /**
     * Save or update the swiper item
     *
     * @param SwiperItem $swiperItem
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(SwiperItem $swiperItem, Request $request)
    {
        try {
            $data = $request->get('swiper_item');
            
            // Проверка и обработка пути к изображению
            if (isset($data['image'])) {
                // Проверка относительного пути
                if (is_string($data['image']) && !empty($data['image'])) {
                    // Удаляем дублирующиеся слеши, если есть
                    $data['image'] = ltrim($data['image'], '/');
                }
            }
            
            $swiperItem->fill($data)->save();
            
            Toast::info('Элемент свайпера был сохранен');
        } catch (\Exception $e) {
            Toast::error('Ошибка: ' . $e->getMessage());
        }
        
        return redirect()->route('platform.swiper');
    }
    
    /**
     * Remove the swiper item
     *
     * @param SwiperItem $swiperItem
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(SwiperItem $swiperItem)
    {
        $swiperItem->delete();
        
        Toast::info('Элемент свайпера был удален');
        
        return redirect()->route('platform.swiper');
    }
}
