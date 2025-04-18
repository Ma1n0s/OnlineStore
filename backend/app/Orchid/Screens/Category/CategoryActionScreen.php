<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class CategoryActionScreen extends Screen
{
    public $category;

    public function query(Category $category): array
    {
        return [
            'category' => $category,
            'children' => $category->children()->paginate(10),
        ];
    }

    public function name(): ?string
    {
        return "Manage: {$this->category->name}";
    }

    public function commandBar(): array
    {
        return [
            Link::make('Back')
                ->icon('arrow-left')
                ->route('platform.category.list'),
                
            Link::make('Edit Category')
                ->icon('pencil')
                ->route('platform.category.edit', $this->category),
                
            Link::make('Add Subcategory')
                ->icon('plus')
                ->route('platform.category.create', ['parent_id' => $this->category->id]),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::view('platform.category.actions', [
                'category' => $this->category,
            ]),
            
            Layout::table('children', [
                \Orchid\Screen\TD::make('name', 'Name')
                    ->render(function (Category $category) {
                        return Link::make($category->name)
                            ->route('platform.category.action', $category);
                    }),
                    
                \Orchid\Screen\TD::make('title', 'Title'),
                
                \Orchid\Screen\TD::make('actions', 'Actions')
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
