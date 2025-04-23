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
                ->orderBy('name')
                ->get()
        ];
    }

    public function name(): ?string
    {
        return $this->category->exists 
            ? "Редактирование категории: {$this->category->name}" 
            : 'Создание новой категории';
    }

    public function description(): ?string
    {
        return $this->category->exists
            ? "Изменение параметров категории {$this->category->name}"
            : 'Заполните поля для создания новой категории';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save')
                ->class('btn btn-success'),
                
            Button::make('Отменить')
                ->icon('close')
                ->method('cancel')
                ->class('btn btn-secondary'),
        ];
    }

    public function layout(): array
    {
        $parentId = request()->input('parent_id');
        $isCreatingSubcategory = !$this->category->exists && $parentId;
        
        return [
            Layout::rows([
                Input::make('category.name')
                    ->title('Название')
                    ->required()
                    ->placeholder('Введите название категории')
                    ->help('Отображается в меню и навигации'),
                    
                Input::make('category.title')
                    ->title('Заголовок')
                    ->placeholder('Введите заголовок для отображения')
                    ->help('Отображается на странице категории'),

                TextArea::make('category.description')
                    ->title('Описание')
                    ->rows(3)
                    ->placeholder('Детальное описание категории')
                    ->help('Отображается на странице категории'),

                $isCreatingSubcategory 
                    ? Input::make('category.parent_id')
                        ->title('Родительская категория')
                        ->value($parentId)
                        ->readonly()
                        ->help('Эта категория будет подкатегорией для: ' . Category::find($parentId)->name)
                    : Select::make('category.parent_id')
                        ->title('Родительская категория')
                        ->empty('Без родительской категории', '0') 
                        ->fromQuery(
                            Category::where('id', '!=', $this->category->id ?? null)
                                ->where(function($query) {
                                    $query->whereNull('parent_id')
                                          ->orWhere('parent_id', '!=', $this->category->id ?? null);
                                })
                                ->orderBy('name'),
                            'name'
                        )
                        ->help('Выберите родительскую категорию, если нужно'),

                Select::make('category.type')
                    ->title('Тип категории')
                    ->options([
                        'category' => 'Категория (может содержать подкатегории)',
                        'product_container' => 'Контейнер товаров (может содержать только товары)',
                    ])
                    ->required()
                    ->help('Определяет, что может содержать эта категория'),

                Input::make('category.slug')
                    ->title('URL-адрес')
                    ->placeholder('автоматически-сгенерируется')
                    ->help('Человекопонятный URL (оставьте пустым для автогенерации)'),

                Upload::make('category.image_url')
                    ->title('Основное изображение')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->storage('public')
                    ->path('categories')
                    ->help('Главное изображение категории (рекомендуемый размер: 800x600)'),

                Upload::make('category.description_image_url')
                    ->title('Дополнительное изображение')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->storage('public')
                    ->path('categories')
                    ->help('Изображение для описания категории (опционально)'),
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
            Alert::error('Категория с таким названием уже существует!');
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

        // Handle image uploads
        if ($request->has('category.image_url')) {
            $image = $request->input('category.image_url');
            if (is_array($image) && !empty($image)) {
                // Delete old image if exists
                if ($category->exists && $category->image_url) {
                    Storage::disk('public')->delete($category->image_url);
                }
                $data['image_url'] = $image[0];
            } elseif (empty($image)) {
                // Image was removed
                if ($category->exists && $category->image_url) {
                    Storage::disk('public')->delete($category->image_url);
                }
                $data['image_url'] = null;
            }
        }

        if ($request->has('category.description_image_url')) {
            $descImage = $request->input('category.description_image_url');
            if (is_array($descImage) && !empty($descImage)) {
                // Delete old image if exists
                if ($category->exists && $category->description_image_url) {
                    Storage::disk('public')->delete($category->description_image_url);
                }
                $data['description_image_url'] = $descImage[0];
            } elseif (empty($descImage)) {
                // Image was removed
                if ($category->exists && $category->description_image_url) {
                    Storage::disk('public')->delete($category->description_image_url);
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
    
    public function cancel()
    {
        if ($this->category->exists && $this->category->parent_id) {
            return redirect()->route('platform.category.action', $this->category->parent);
        }
        
        return redirect()->route('platform.category.list');
    }
}