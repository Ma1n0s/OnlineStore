<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class CategoryEditScreen extends Screen
{
    public $category;
    public $hasProducts;

    public function query(Category $category): array
    {
        $parentId = request()->input('parent_id');
        $hasProducts = false;
        
        if ($parentId) {
            $hasProducts = Product::where('category_id', $parentId)->exists();
        }
        
        return [
            'category' => $category,
            'hasProducts' => $hasProducts,
            'parentCategories' => Category::when($category->exists, function($query) use ($category) {
                    $query->whereNotIn('id', $category->descendants()->pluck('id'))
                        ->where('id', '!=', $category->id);
                })
                ->when($parentId, function($query) use ($parentId) {
                    $query->where('id', $parentId);
                }, function($query) {
                    $query->whereNull('parent_id');
                })
                ->get()
        ];
    }

    public function name(): ?string
    {
        return $this->category->exists ? 'Редактирование категории' : 'Создание категории';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function layout(): array
    {
        $parentId = request()->input('parent_id');
        $isCreatingSubcategory = !$this->category->exists && $parentId;
        
        $imageIds = [];
        $descImageIds = [];

        if ($this->category->exists) {
            if ($this->category->image_url) {
                $imageIds = [
                    $this->category->image_url
                ];
            }

            if ($this->category->description_image_url) {
                $descImageIds = [
                    $this->category->description_image_url
                ];
            }
        }

        return [
            Layout::rows([
                Input::make('category.name')
                    ->title('Название')
                    ->placeholder('Введите название категории')
                    ->required()
                    ->help('Уникальное название категории'),
                    
                Input::make('category.title')
                    ->title('Заголовок')
                    ->placeholder('Отображаемый заголовок')
                    ->help('Заголовок для отображения на сайте'),
                    
                TextArea::make('category.description')
                    ->title('Описание')
                    ->rows(3)
                    ->placeholder('Описание категории')
                    ->help('Подробное описание категории'),

                $isCreatingSubcategory 
                    ? ($this->hasProducts 
                        ? Input::make('category.parent_id')
                            ->title('Родительская категория')
                            ->value($parentId)
                            ->readonly()
                            ->help('Нельзя создать подкатегорию - в этой категории уже есть товары')
                        : Input::make('category.parent_id')
                            ->title('Родительская категория')
                            ->value($parentId)
                            ->readonly()
                            ->help('Эта категория будет подкатегорией для: ' . Category::find($parentId)->name))
                    : Select::make('category.parent_id')
                        ->title('Родительская категория')
                        ->empty('Без родителя (корневая категория)', '0')
                        ->fromQuery(
                            Category::when($this->category->exists, function($query) {
                                $query->whereNotIn('id', $this->category->descendants()->pluck('id'))
                                    ->where('id', '!=', $this->category->id);
                            }),
                            'name'
                        )
                        ->help('Выберите родительскую категорию, если нужно'),

                Input::make('category.slug')
                    ->title('URL-адрес')
                    ->placeholder('URL-адрес категории')
                    ->help('Оставьте пустым для автоматической генерации из названия'),

                Upload::make('category.image_url')
                    ->title('Основное изображение')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->storage('public')
                    ->path('categories')
                    ->value($imageIds)
                    ->groups('category_main')
                    ->target('category.image_url'),

                Upload::make('category.description_image_url')
                    ->title('Дополнительное изображение')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->storage('public')
                    ->path('categories')
                    ->value($descImageIds)
                    ->groups('category_description')
                    ->target('category.description_image_url'),
            ]),
        ];
    }

    public function save(Category $category, Request $request)
    {
        $data = $request->get('category');
        
        // Validation
        $exists = Category::where('name', $data['name'])
            ->where('id', '!=', $category->id ?? null)
            ->exists();
            
        if ($exists) {
            Alert::error('Category name already exists!');
            return back();
        }
        
        // Handle parent_id
        if (isset($data['parent_id']) && $data['parent_id'] === '0') {
            $data['parent_id'] = null;
        }
        
        // Set parent_id for new subcategories
        if (!$category->exists && $request->has('parent_id')) {
            $data['parent_id'] = $request->input('parent_id');
        }
        
        // Generate slug if empty
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Ensure unique slug
        $slugExists = Category::where('slug', $data['slug'])
            ->where('id', '!=', $category->id ?? null)
            ->exists();
            
        if ($slugExists) {
            $data['slug'] = $data['slug'] . '-' . uniqid();
        }

        // Логируем данные перед обработкой
        Log::info('Category save - data received', [
            'category_id' => $category->id,
            'category_exists' => $category->exists,
            'image_url_input' => $request->input('category.image_url'),
            'desc_image_url_input' => $request->input('category.description_image_url'),
        ]);

        // Временно храним ID вложений
        $mainImageAttachmentId = null;
        $descImageAttachmentId = null;
        
        // Обработка основного изображения
        if ($request->has('category.image_url')) {
            $mainImage = $request->input('category.image_url');
            
            if (is_array($mainImage)) {
                if (empty($mainImage)) {
                    // Пользователь удалил изображение
                    if ($category->exists && $category->image_url) {
                        $attachment = Attachment::find($category->image_url);
                        if ($attachment) {
                            // Проверяем, используется ли файл где-то еще
                            $usageCount = $this->getAttachmentUsageCount($attachment->id);
                            
                            Log::info('Checking if main image can be deleted', [
                                'attachment_id' => $attachment->id,
                                'usage_count' => $usageCount
                            ]);
                            
                            // Если файл нигде не используется (или используется только в текущей категории)
                            if ($usageCount <= 1) {
                                $attachment->delete();
                                Log::info('Deleted unused main image', [
                                    'attachment_id' => $attachment->id
                                ]);
                            } else {
                                Log::info('Main image is used elsewhere, not deleting', [
                                    'attachment_id' => $attachment->id,
                                    'usage_count' => $usageCount
                                ]);
                            }
                        }
                    }
                    $data['image_url'] = null;
                } else {
                    // Новое изображение загружено
                    $mainImageAttachmentId = $mainImage[0];
                    $data['image_url'] = $mainImageAttachmentId;
                    
                    // Обновляем группу аттачмента
                    $attachment = Attachment::find($mainImageAttachmentId);
                    if ($attachment) {
                        $attachment->update(['group' => 'category_main']);
                    }
                }
            } else {
                // Если это не массив, сохраняем текущее значение
                if ($category->exists) {
                    $data['image_url'] = $category->image_url;
                }
            }
        } elseif ($category->exists) {
            // Если поле отсутствует, сохраняем текущее значение
            $data['image_url'] = $category->image_url;
        }

        // Обработка изображения описания
        if ($request->has('category.description_image_url')) {
            $descImage = $request->input('category.description_image_url');
            
            if (is_array($descImage)) {
                if (empty($descImage)) {
                    // Пользователь удалил изображение
                    if ($category->exists && $category->description_image_url) {
                        $attachment = Attachment::find($category->description_image_url);
                        if ($attachment) {
                            // Проверяем, используется ли файл где-то еще
                            $usageCount = $this->getAttachmentUsageCount($attachment->id);
                            
                            Log::info('Checking if description image can be deleted', [
                                'attachment_id' => $attachment->id,
                                'usage_count' => $usageCount
                            ]);
                            
                            // Если файл нигде не используется (или используется только в текущей категории)
                            if ($usageCount <= 1) {
                                $attachment->delete();
                                Log::info('Deleted unused description image', [
                                    'attachment_id' => $attachment->id
                                ]);
                            } else {
                                Log::info('Description image is used elsewhere, not deleting', [
                                    'attachment_id' => $attachment->id,
                                    'usage_count' => $usageCount
                                ]);
                            }
                        }
                    }
                    $data['description_image_url'] = null;
                } else {
                    // Новое изображение загружено
                    $descImageAttachmentId = $descImage[0];
                    $data['description_image_url'] = $descImageAttachmentId;
                    
                    // Обновляем группу аттачмента
                    $attachment = Attachment::find($descImageAttachmentId);
                    if ($attachment) {
                        $attachment->update(['group' => 'category_description']);
                    }
                }
            } else {
                // Если это не массив, сохраняем текущее значение
                if ($category->exists) {
                    $data['description_image_url'] = $category->description_image_url;
                }
            }
        } elseif ($category->exists) {
            // Если поле отсутствует, сохраняем текущее значение
            $data['description_image_url'] = $category->description_image_url;
        }
        
        // Логируем данные перед сохранением
        Log::info('Category save - before save', [
            'data' => $data,
            'mainImageAttachmentId' => $mainImageAttachmentId,
            'descImageAttachmentId' => $descImageAttachmentId
        ]);
        
        // Сохраняем категорию
        $category->fill($data)->save();
        
        // ВАЖНО: Только после сохранения категории привязываем вложения
        if ($mainImageAttachmentId) {
            try {
                // Используем базовый метод без дополнительных параметров
                $category->attachment()->syncWithoutDetaching([$mainImageAttachmentId]);
                Log::info('Attached main image', [
                    'category_id' => $category->id,
                    'attachment_id' => $mainImageAttachmentId
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to attach main image', [
                    'error' => $e->getMessage(),
                    'category_id' => $category->id,
                    'attachment_id' => $mainImageAttachmentId
                ]);
            }
        }
        
        if ($descImageAttachmentId) {
            try {
                // Используем базовый метод без дополнительных параметров
                $category->attachment()->syncWithoutDetaching([$descImageAttachmentId]);
                Log::info('Attached description image', [
                    'category_id' => $category->id,
                    'attachment_id' => $descImageAttachmentId
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to attach description image', [
                    'error' => $e->getMessage(),
                    'category_id' => $category->id,
                    'attachment_id' => $descImageAttachmentId
                ]);
            }
        }

        Alert::success('Category was saved');
        
        if ($category->parent_id) {
            return redirect()->route('platform.category.action', $category->parent);
        }
        
        return redirect()->route('platform.category.list');
    }
    
    /**
     * Проверяет количество использований вложения в таблице attachment_models
     * 
     * @param int $attachmentId ID вложения
     * @return int Количество моделей, использующих данное вложение
     */
    private function getAttachmentUsageCount(int $attachmentId): int
    {
        try {
            $count = DB::table('attachmentable')
                ->where('attachment_id', $attachmentId)
                ->count();
                
            return $count;
        } catch (\Exception $e) {
            Log::error('Error checking attachment usage', [
                'attachment_id' => $attachmentId,
                'error' => $e->getMessage()
            ]);
            
            // В случае ошибки возвращаем 0, чтобы файл был удален
            return 0;
        }
    }
}