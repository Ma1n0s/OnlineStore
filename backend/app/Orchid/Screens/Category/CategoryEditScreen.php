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

                Upload::make('category.image')
                    ->title('Category Image')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->storage('public')
                    ->path('categories'),

                Upload::make('category.description_image')
                    ->title('Description Image')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->storage('public')
                    ->path('categories'),
            ]),
        ];
    }

    public function save(Category $category, Request $request)
    {
        $data = $request->get('category');
        
        $exists = Category::where('name', $data['name'])
            ->where('id', '!=', $category->id ?? null)
            ->exists();
            
        if ($exists) {
            Alert::error('Category name already exists!');
            return back();
        }
        
        if (isset($data['parent_id']) && $data['parent_id'] === '0') {
            $data['parent_id'] = null;
        }
        
        if (!$category->exists && $request->has('parent_id')) {
            $data['parent_id'] = $request->input('parent_id');
        }
        
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        

        $slugExists = Category::where('slug', $data['slug'])
            ->where('id', '!=', $category->id ?? null)
            ->exists();
            
        if ($slugExists) {
            $data['slug'] = $data['slug'] . '-' . uniqid();
        }

        if ($request->has('category.image')) {
                $image = $request->input('category.image');
                if (is_array($image) && !empty($image)) {
                    if ($category->exists && $category->image_url) {
                        Storage::disk('public')->delete($category->image_url);
                    }
                    $data['image_url'] = 'categories/'.basename($image[0]);
                } elseif (empty($image)) {
                    if ($category->exists && $category->image_url) {
                        Storage::disk('public')->delete($category->image_url);
                    }
                    $data['image_url'] = null;
                }
            }

            if ($request->has('category.description_image')) {
                $descImage = $request->input('category.description_image');
                if (is_array($descImage) && !empty($descImage)) {
                    if ($category->exists && $category->description_image_url) {
                        Storage::disk('public')->delete($category->description_image_url);
                    }
                    $data['description_image_url'] = 'categories/'.basename($descImage[0]);
                } elseif (empty($descImage)) {
                    if ($category->exists && $category->description_image_url) {
                        Storage::disk('public')->delete($category->description_image_url);
                    }
                    $data['description_image_url'] = null;
                }
            }

        if ($request->has('category.description_image')) {
            $descImage = $request->input('category.description_image');
            if (is_array($descImage) && !empty($descImage)) {
                if ($category->exists && $category->description_image_url) {
                    Storage::disk('public')->delete($category->description_image_url);
                }
                $data['description_image_url'] = str_replace('public/', '', $descImage[0]);
            } elseif (empty($descImage)) {
                if ($category->exists && $category->description_image_url) {
                    Storage::disk('public')->delete($category->description_image_url);
                }
                $data['description_image_url'] = null;
            }
        }

        $category->fill($data)->save();

        Alert::success('Category was saved');
        
        if ($category->parent_id) {
            return redirect()->route('platform.category.action', $category->parent);
        }
        
        return redirect()->route('platform.category.list');
    }
}