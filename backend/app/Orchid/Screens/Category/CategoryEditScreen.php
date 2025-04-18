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

class CategoryEditScreen extends Screen
{
    public $category;

    public function query(Category $category): array
    {
        return [
            'category' => $category,
            'parentCategories' => Category::where('id', '!=', $category->id)
                ->where(function($query) use ($category) {
                    $query->whereNull('parent_id')
                        ->orWhere('parent_id', '!=', $category->id);
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                }),
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

                Select::make('category.parent_id')
                    ->title('Parent Category')
                    ->empty('No parent', '0') // Исправлено здесь
                    ->fromQuery(
                        Category::where('id', '!=', $this->category->id)
                            ->where(function($query) {
                                $query->whereNull('parent_id')
                                      ->orWhere('parent_id', '!=', $this->category->id);
                            }),
                        'name'
                    ),

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
        
        // Преобразуем '0' обратно в null для parent_id
        if (isset($data['parent_id']) && $data['parent_id'] === '0') {
            $data['parent_id'] = null;
        }
        
        // Обработка загрузки изображений
        if ($request->hasFile('category.image_url')) {
            $data['image_url'] = $request->file('category.image_url')->store('categories', 'public');
        }
        
        if ($request->hasFile('category.description_image_url')) {
            $data['description_image_url'] = $request->file('category.description_image_url')->store('categories', 'public');
        }

        $category->fill($data)->save();

        Alert::success('Category was saved');

        return redirect()->route('platform.category.list');
    }
}