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
        return $this->category->exists ? 'Edit Category' : 'Create Category';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Save')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function layout(): array
    {
        $parentId = request()->input('parent_id');
        $isCreatingSubcategory = !$this->category->exists && $parentId;
        
        // Подготовка данных для полей Upload
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
                    ->title('Name')
                    ->required(),
                    
                Input::make('category.title')
                    ->title('Display Title'),
                    
                TextArea::make('category.description')
                    ->title('Description')
                    ->rows(3),

                $isCreatingSubcategory 
                    ? Input::make('category.parent_id')
                        ->title('Parent Category')
                        ->value($parentId)
                        ->readonly()
                        ->help('This category will be a subcategory of: ' . Category::find($parentId)->name)
                    : Select::make('category.parent_id')
                        ->title('Parent Category')
                        ->empty('No parent (root category)', '0')
                        ->fromQuery(
                            Category::when($this->category->exists, function($query) {
                                $query->whereNotIn('id', $this->category->descendants()->pluck('id'))
                                    ->where('id', '!=', $this->category->id);
                            }),
                            'name'
                        ),

                Input::make('category.slug')
                    ->title('Slug')
                    ->help('Leave empty to auto-generate from name'),

                Upload::make('category.image_url')
                    ->title('Main Image')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->storage('public')
                    ->path('categories')
                    ->value($imageIds)
                    ->groups('category_main')
                    ->target('category.image_url'),

                Upload::make('category.description_image_url')
                    ->title('Description Image')
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

        // Логируем данные перед обработкой, чтобы понимать что получаем
        Log::info('Category save - raw data received', [
            'id' => $category->id,
            'image_url_input' => $request->input('category.image_url'),
            'desc_image_url_input' => $request->input('category.description_image_url'),
            'current_image_url' => $category->exists ? $category->image_url : null,
            'current_desc_image_url' => $category->exists ? $category->description_image_url : null,
        ]);

        // Основное изображение
        if ($request->has('category.image_url')) {
            $mainImage = $request->input('category.image_url');
            
            if (is_array($mainImage)) {
                if (empty($mainImage)) {
                    // Пользователь удалил изображение
                    if ($category->exists && $category->image_url) {
                        $attachment = Attachment::find($category->image_url);
                        if ($attachment) {
                            $attachment->delete();
                        }
                    }
                    $data['image_url'] = null;
                } else {
                    // Новое изображение загружено
                    $data['image_url'] = $mainImage[0];
                    // Обновляем группу аттачмента
                    $attachment = Attachment::find($mainImage[0]);
                    if ($attachment) {
                        $attachment->update(['group' => 'category_main']);
                        
                        // Привязываем attachment к категории
                        $category->attachment()->syncWithoutDetaching([$attachment->id => [
                            'attachmentable_type' => Category::class,
                            'attachmentable_id' => $category->id
                        ]]);
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

        // Изображение описания
        if ($request->has('category.description_image_url')) {
            $descImage = $request->input('category.description_image_url');
            
            if (is_array($descImage)) {
                if (empty($descImage)) {
                    // Пользователь удалил изображение
                    if ($category->exists && $category->description_image_url) {
                        $attachment = Attachment::find($category->description_image_url);
                        if ($attachment) {
                            $attachment->delete();
                        }
                    }
                    $data['description_image_url'] = null;
                } else {
                    // Новое изображение загружено
                    $data['description_image_url'] = $descImage[0];
                    // Обновляем группу аттачмента
                    $attachment = Attachment::find($descImage[0]);
                    if ($attachment) {
                        $attachment->update(['group' => 'category_description']);
                        
                        // Привязываем attachment к категории
                        $category->attachment()->syncWithoutDetaching([$attachment->id => [
                            'attachmentable_type' => Category::class,
                            'attachmentable_id' => $category->id
                        ]]);
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

        // Логируем финальные данные для сохранения
        Log::info('Category save - final data', [
            'image_url' => $data['image_url'] ?? null,
            'description_image_url' => $data['description_image_url'] ?? null,
        ]);
        
        $category->fill($data)->save();

        Alert::success('Category was saved');
        
        if ($category->parent_id) {
            return redirect()->route('platform.category.action', $category->parent);
        }
        
        return redirect()->route('platform.category.list');
    }
}