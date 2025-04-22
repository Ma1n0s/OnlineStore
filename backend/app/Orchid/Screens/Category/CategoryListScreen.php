<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

class CategoryListScreen extends Screen
{
    public function query(): array
    {
        return [
            'categories' => Category::with(['parent', 'children'])
                ->orderBy('parent_id')
                ->orderBy('name')
                ->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Categories';
    }

    public function commandBar(): array
    {
        return [
            Link::make('Create Root Category')
                ->icon('plus')
                ->route('platform.category.create'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('categories', [
                TD::make('name', 'Name')
                    ->render(function (Category $category) {
                        $indent = str_repeat('&nbsp;&nbsp;', $category->getPath()->count() - 1);
                        return Link::make($indent . $category->name)
                            ->route('platform.category.action', $category);
                    }),
                    
                TD::make('title', 'Title'),
                
                TD::make('parent.name', 'Parent')
                    ->render(function (Category $category) {
                        return $category->parent
                            ? Link::make($category->parent->name)
                                ->route('platform.category.action', $category->parent)
                            : 'Root';
                    }),
                
                TD::make('products_count', 'Products')
                    ->render(function (Category $category) {
                        return $category->products()->count();
                    }),
                
                TD::make('actions', 'Actions')
                    ->alignRight()
                    ->render(function (Category $category) {
                        return DropDown::make()
                            ->icon('three-dots-vertical')
                            ->list([
                                Link::make('Edit')
                                    ->route('platform.category.edit', $category)
                                    ->icon('pencil')
                                    ->canSee(auth()->user()->hasAccess('platform.categories.edit')),
                                    
                                Link::make('Add Subcategory')
                                    ->route('platform.category.create', ['parent_id' => $category->id])
                                    ->icon('plus')
                                    ->canSee(auth()->user()->hasAccess('platform.categories.create')),
                                    
                                Button::make('Delete')
                                    ->icon('trash')
                                    ->method('remove')
                                    ->confirm('Are you sure you want to delete this category?')
                                    ->parameters(['id' => $category->id])
                                    ->canSee(auth()->user()->hasAccess('platform.categories.delete')),
                            ]);
                    }),
            ]),
        ];
    }

    public function remove(Request $request)
    {
        $category = Category::findOrFail($request->get('id'));
        
        if ($category->children()->exists()) {
            Alert::error('Cannot delete category - it has subcategories');
            return back();
        }
        
        if ($category->products()->exists()) {
            Alert::error('Cannot delete category - it contains products');
            return back();
        }
        
        if ($category->image_url) {
            Storage::disk('public')->delete($category->image_url);
        }
        if ($category->description_image_url) {
            Storage::disk('public')->delete($category->description_image_url);
        }
        
        $category->delete();
        
        Alert::info('Category deleted successfully');
        return back();
    }
}