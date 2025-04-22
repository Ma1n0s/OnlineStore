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
use Illuminate\Support\Facades\Validator;
use Orchid\Support\Facades\Layout;
use Illuminate\Support\Str;

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
        
        return [
            Layout::rows([
                Input::make('category.name')
                    ->title('Name')
                    ->required(),
                    
                Input::make('category.title')
                    ->title('Display Title')
                    ->help('This will be shown to users'),

                TextArea::make('category.description')
                    ->title('Description')
                    ->rows(3),

                // Измененное поле для выбора родительской категории
                $isCreatingSubcategory 
                    ? Input::make('category.parent_id')
                        ->title('Parent Category')
                        ->value($parentId)
                        ->readonly()
                        ->help('This category will be a subcategory of: ' . Category::find($parentId)->name)
                    : Select::make('category.parent_id')
                        ->title('Parent Category')
                        ->empty('No parent', '0') 
                        ->fromQuery(
                            Category::where('id', '!=', $this->category->id)
                                ->where(function($query) {
                                    $query->whereNull('parent_id')
                                          ->orWhere('parent_id', '!=', $this->category->id);
                                }),
                            'name'
                        ),

                Select::make('category.type')
                    ->title('Type')
                    ->options([
                        'category' => 'Category (can contain subcategories)',
                        'product_container' => 'Product Container (can only contain products)',
                    ])
                    ->required(),

                Input::make('category.slug')
                    ->title('Slug')
                    ->help('Leave empty to auto-generate from name'),

                Upload::make('category.image_url')
                    ->title('Category Image')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1),

                Upload::make('category.description_image_url')
                    ->title('Description Image')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1),
            ]),
        ];
    }

    public function save(Category $category, Request $request)
    {
        $data = $request->get('category');
        
        // Проверка на уникальность имени
        $exists = Category::where('name', $data['name'])
            ->where('id', '!=', $category->id ?? null)
            ->exists();
            
        if ($exists) {
            Alert::error('Category name already exists!');
            return back();
        }
        
        // Обработка parent_id
        if (isset($data['parent_id']) && $data['parent_id'] === '0') {
            $data['parent_id'] = null;
        }
        
        // Если создается подкатегория, устанавливаем parent_id из запроса
        if (!$category->exists && $request->has('parent_id')) {
            $data['parent_id'] = $request->input('parent_id');
        }
        
        // Генерация slug если не указан
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
        
        if ($request->hasFile('category.image_url')) {
            $data['image_url'] = $request->file('category.image_url')->store('categories', 'public');
        }
        
        if ($request->hasFile('category.description_image_url')) {
            $data['description_image_url'] = $request->file('category.description_image_url')->store('categories', 'public');
        }

        $category->fill($data)->save();

        Alert::success('Category was saved');
        
        // Перенаправляем на страницу родительской категории, если это подкатегория
        if ($category->parent_id) {
            return redirect()->route('platform.category.action', $category->parent);
        }
        
        return redirect()->route('platform.category.list');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $baseSlug = $slug = Str::slug($category->name);
                $count = 1;
                
                while (Category::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $count++;
                }
                
                $category->slug = $slug;
            }
        });
    }
}