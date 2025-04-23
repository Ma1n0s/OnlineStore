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

class CategoryEditScreen extends Screen
{
    public $category;

    public function query(Category $category): array
    {
        $parentId = request()->input('parent_id');
        
        return [
            'category' => $category,
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

    public function description(): ?string
    {
        return $this->category->exists 
            ? "Редактирование категории {$this->category->name}"
            : "Создание новой категории";
    }

    public function commandBar(): array
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save')
                ->class('btn btn-success'),
                
            Button::make('Отмена')
                ->icon('close')
                ->route('platform.category.list')
                ->canSee(!$this->category->exists),
                
            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->confirm('Вы уверены, что хотите удалить эту категорию?')
                ->canSee($this->category->exists),
        ];
    }

    public function layout(): array
    {
        $parentId = request()->input('parent_id');
        $isCreatingSubcategory = !$this->category->exists && $parentId;
        
        return [
            Layout::block([
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
                        ? Input::make('category.parent_id')
                            ->title('Родительская категория')
                            ->value($parentId)
                            ->readonly()
                            ->help('Эта категория будет подкатегорией для: ' . Category::find($parentId)->name)
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
                ])
            ])
            ->title('Основная информация'),
            
            Layout::block([
                Layout::rows([
                    Upload::make('category.image_url')
                        ->title('Основное изображение')
                        ->acceptedFiles('image/*')
                        ->maxFiles(1)
                        ->storage('public')
                        ->path('categories')
                        ->help('Основное изображение категории'),
                        
                    Upload::make('category.description_image_url')
                        ->title('Дополнительное изображение')
                        ->acceptedFiles('image/*')
                        ->maxFiles(1)
                        ->storage('public')
                        ->path('categories')
                        ->help('Изображение для описания категории'),
                ])
            ])
            ->title('Изображения')
            ->description('Загрузите изображения для категории'),
        ];
    }

    public function save(Category $category, Request $request)
    {
        $data = $request->get('category');
        
        // Валидация
        $exists = Category::where('name', $data['name'])
            ->where('id', '!=', $category->id ?? null)
            ->exists();
            
        if ($exists) {
            Alert::error('Категория с таким названием уже существует!');
            return back();
        }
        
        // Обработка parent_id
        if (isset($data['parent_id']) && $data['parent_id'] === '0') {
            $data['parent_id'] = null;
        }
        
        // Установка parent_id для новых подкатегорий
        if (!$category->exists && $request->has('parent_id')) {
            $data['parent_id'] = $request->input('parent_id');
        }
        
        // Генерация slug если пустой
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Проверка уникальности slug
        $slugExists = Category::where('slug', $data['slug'])
            ->where('id', '!=', $category->id ?? null)
            ->exists();
            
        if ($slugExists) {
            $data['slug'] = $data['slug'] . '-' . uniqid();
        }

        // Обработка загрузки изображений
        if ($request->has('category.image_url')) {
            $image = $request->input('category.image_url');
            if (is_array($image) && !empty($image)) {
                // Удаление старого изображения если есть
                if ($category->exists && $category->image_url) {
                    $attachment = Attachment::find($category->image_url);
                    if ($attachment) {
                        $attachment->delete();
                    }
                }
                
                $attachment = Attachment::find($image[0]);
                if ($attachment) {
                    $data['image_url'] = $image[0];
                }
            } elseif (empty($image)) {
                // Изображение было удалено
                if ($category->exists && $category->image_url) {
                    $attachment = Attachment::find($category->image_url);
                    if ($attachment) {
                        $attachment->delete();
                    }
                }
                $data['image_url'] = null;
            }
        }

        if ($request->has('category.description_image_url')) {
            $descImage = $request->input('category.description_image_url');
            if (is_array($descImage) && !empty($descImage)) {
                // Удаление старого изображения если есть
                if ($category->exists && $category->description_image_url) {
                    $attachment = Attachment::find($category->description_image_url);
                    if ($attachment) {
                        $attachment->delete();
                    }
                }
                
                $attachment = Attachment::find($descImage[0]);
                if ($attachment) {
                    $data['description_image_url'] = $descImage[0];
                }
            } elseif (empty($descImage)) {
                // Изображение было удалено
                if ($category->exists && $category->description_image_url) {
                    $attachment = Attachment::find($category->description_image_url);
                    if ($attachment) {
                        $attachment->delete();
                    }
                }
                $data['description_image_url'] = null;
            }
        }

        $category->fill($data)->save();

        Alert::success('Категория успешно сохранена');
        
        if ($category->parent_id) {
            return redirect()->route('platform.category.action', $category->parent);
        }
        
        return redirect()->route('platform.category.list');
    }
    
    public function remove(Category $category)
    {
        $this->deleteCategoryImages($category);
        $category->delete();
        
        Alert::info('Категория успешно удалена');
        return redirect()->route('platform.category.list');
    }
    
    protected function deleteCategoryImages(Category $category)
    {
        if ($category->image_url) {
            Storage::disk('public')->delete($category->image_url);
        }
        if ($category->description_image_url) {
            Storage::disk('public')->delete($category->description_image_url);
        }
    }
}