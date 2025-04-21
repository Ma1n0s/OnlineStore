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
            'products' => $category->products()->paginate(10)
        ];
    }

    public function name(): ?string
    {
        return $this->category->name;
    }

    public function commandBar(): array
    {
        return [
            Link::make('Back')
                ->icon('arrow-left')
                ->route('platform.category.list'),
                
            Link::make('Edit')
                ->icon('pencil')
                ->route('platform.category.edit', $this->category),
                
            Link::make('Add Subcategory')
                ->icon('plus')
                ->route('platform.category.create', ['parent_id' => $this->category->id]),
                
            Link::make('Add Product')
                ->icon('plus')
                ->route('platform.product.create', ['category_id' => $this->category->id])
                ->canSee($this->category->canHaveProducts()),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::view('platform.category.view', [
                'category' => $this->category
            ]),
            
            Layout::tabs([
                'Subcategories' => Layout::table('children', [
                    TD::make('name', 'Name')
                        ->render(function (Category $category) {
                            return Link::make($category->name)
                                ->route('platform.category.action', $category);
                        }),
                    TD::make('actions', 'Actions')
                        ->render(function (Category $category) {
                            return Link::make('Edit')
                                ->route('platform.category.edit', $category);
                        }),
                ]),
                
                'Products' => Layout::table('products', [
                    TD::make('name', 'Name')
                        ->render(function (Product $product) {
                            return Link::make($product->name)
                                ->route('platform.product.edit', $product);
                        }),
                    TD::make('price', 'Price'),
                    TD::make('actions', 'Actions')
                        ->render(function (Product $product) {
                            return Link::make('Edit')
                                ->route('platform.product.edit', $product);
                        }),
                ])->canSee($this->category->canHaveProducts()),
            ]),
        ];
    }
}