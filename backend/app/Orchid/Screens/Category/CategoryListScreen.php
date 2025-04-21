<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class CategoryListScreen extends Screen
{
    public function query(): array
    {
        return [
            'categories' => Category::with('parent')
                ->orderBy('id', 'desc')
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
                        return Link::make('Edit')
                            ->route('platform.category.edit', $category)
                            ->icon('pencil');
                    }),
            ]),
        ];
    }
}