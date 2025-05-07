<?php

namespace App\Orchid\Screens;

use App\Models\News;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Support\Facades\Log;

class NewsScreen extends Screen
{
    /**
     * @var News
     */
    public $News;

    /**
     * Конструктор класса.
     *
     * @param News|null $news
     */
    public function __construct(News $news = null)
    {
        $this->News = $news ?? new News();
    }

    /**
     * Получение данных для отображения.
     *
     * @param News $news
     * @return array
     */
    public function query(News $news): array
    {
        $this->News = $news;
        
        return [
            'News' => $this->News
        ];
    }

    public function name(): ?string
    {
        return $this->News->exists ? 'Редактирование новости' : 'Создание новости';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save'),

            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->confirm('Вы уверены, что хотите удалить эту новость?')
                ->canSee($this->News->exists),
        ];
    }

    public function layout(): array
    {
        // Подготовка данных для поля Upload
        $imageIds = [];
        
        if ($this->News->exists && $this->News->image) {
            $imageIds = [
                $this->News->image
            ];
        }
        
        return [
            Layout::rows([
                Input::make('News.title')
                    ->title('Заголовок')
                    ->required(),
                    
                Upload::make('News.image')
                    ->title('Изображение')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->storage('public')
                    ->path('news')
                    ->value($imageIds)
                    ->groups('news')
                    ->target('News.image'),

                Quill::make('News.description')
                    ->title('Описание')
                    ->height('300px'),
            ]),
        ];
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'News.title' => 'required|string|max:255',
            'News.image' => 'nullable',
            'News.description' => 'nullable|string',
        ]);

        Log::info('News save - data received', [
            'news_id' => $this->News->id,
            'news_exists' => $this->News->exists,
            'image_input' => $request->input('News.image'),
        ]);

        // Временно храним ID вложения
        $imageAttachmentId = null;
        
        // Обработка изображения
        if ($request->has('News.image')) {
            $image = $request->input('News.image');
            
            if (is_array($image)) {
                if (empty($image)) {
                    // Пользователь удалил изображение
                    if ($this->News->exists && $this->News->image) {
                        $attachment = Attachment::find($this->News->image);
                        if ($attachment) {
                            $attachment->delete();
                            Log::info('Deleted news image', [
                                'attachment_id' => $attachment->id
                            ]);
                        }
                    }
                    $data['News']['image'] = null;
                } else {
                    // Новое изображение загружено
                    $imageAttachmentId = $image[0];
                    $data['News']['image'] = $imageAttachmentId;
                    
                    // Обновляем группу аттачмента
                    $attachment = Attachment::find($imageAttachmentId);
                    if ($attachment) {
                        $attachment->update(['group' => 'news']);
                    }
                }
            } else {
                // Если это не массив, сохраняем текущее значение
                if ($this->News->exists) {
                    $data['News']['image'] = $this->News->image;
                }
            }
        } elseif ($this->News->exists) {
            // Если поле отсутствует, сохраняем текущее значение
            $data['News']['image'] = $this->News->image;
        }

        $this->News->fill($data['News'])->save();
        
        // Привязываем вложение к модели News
        if ($imageAttachmentId) {
            try {
                $this->News->attachment()->syncWithoutDetaching([$imageAttachmentId]);
                Log::info('Attached image to news', [
                    'news_id' => $this->News->id,
                    'attachment_id' => $imageAttachmentId
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to attach image to news', [
                    'error' => $e->getMessage(),
                    'news_id' => $this->News->id,
                    'attachment_id' => $imageAttachmentId
                ]);
            }
        }

        Alert::success('Новость успешно сохранена.');

        return redirect()->route('platform.news.list');
    }

    public function remove()
    {
        if (!$this->News || !$this->News->exists) {
            Alert::error('Ошибка: Новость не найдена.');
            return redirect()->route('platform.news.list');
        }
        
        $this->News->delete();
        Alert::success('Новость успешно удалена.');
        return redirect()->route('platform.news.list');
    }
}