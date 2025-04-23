<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use App\Models\Product;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Illuminate\Support\Facades\Storage;

class CategoryListScreen extends Screen
{
    public function query(): array
    {
        return [
            'categories' => Category::with(['parent', 'children'])
                ->withCount('children')
                ->orderBy('parent_id')
                ->orderBy('name')
                ->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Категории товаров';
    }

    public function description(): ?string
    {
        return 'Полный список категорий и подкатегорий товаров';
    }

    public function commandBar(): array
    {
        return [
            Link::make('Добавить корневую категорию')
                ->icon('plus')
                ->route('platform.category.create'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('products', [
                TD::make('id', 'ID')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->id;
                    }),

                TD::make('name', 'Name')
                    ->sort()
                    ->filter(Input::make())
                    ->render(function (Product $product) {
                        return Link::make($product->name)
                            ->route('platform.product.edit', $product);
                    }),

                TD::make('code', 'Code')
                    ->sort()
                    ->filter(Input::make())
                     ->render(function (Product $product) {
                        return $product->code;
                    }),

                TD::make('price', 'Price')
                    ->sort()
                    ->render(function (Product $product) {
                        return '₽' . number_format((float)$product->price, 2);
                    }),

                TD::make('brand', 'Brand')
                    ->sort()
                    ->filter(Input::make())
                    ->render(function (Product $product) {
                        return $product->brand;
                    }),

                TD::make('slug', 'Slug')
                    ->sort()
                    ->filter(Input::make())
                    ->render(function (Product $product) {
                        return $product->slug;
                    }),

                TD::make('subcategory.name', 'Subcategory')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->subcategory ? $product->subcategory->name : '-';
                    }),

                TD::make('rating', 'Rating')
                    ->sort()
                    ->render(function (Product $product) {
                        return number_format($product->rating, 1);
                    }),

                // TD::make('category_id', 'Category')
                //     ->render(function (Product $product) {
                //         return $product->category->name ?? '-';
                //     }),

                TD::make('created_at', 'Created')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->created_at->toDateTimeString();
                    }),

                TD::make('actions', 'Actions')
                    ->alignRight()
                    ->render(function (Product $product) {
                        return DropDown::make()
                            ->icon('three-dots-vertical')
                            ->list([
                                Link::make('Edit')
                                    ->route('platform.product.edit', $product)
                                    ->icon('pencil')
                                    ->canSee(auth()->user()->hasAccess('platform.products.edit')),
                                    
                                Button::make('Delete')
                                    ->icon('trash')
                                    ->method('remove')
                                    ->confirm('Are you sure you want to delete this product?')
                                    ->parameters(['id' => $product->id])
                                    ->canSee(auth()->user()->hasAccess('platform.products.delete')),
                            ]);
                    }),
            ]),
        ];
    }

    public function removeCategory(Request $request)
    {
        $category = Category::findOrFail($request->get('id'));
        
        // Получаем все ID категорий для удаления
        $categoryIds = $category->descendants()->pluck('id')->push($category->id);
        
        // Удаляем все товары
        Product::whereIn('subcategory_id', $categoryIds)->delete();
        
        // Удаляем все подкатегории
        $category->descendants()->delete();
        
        // Удаляем саму категорию
        $category->delete();

        Alert::success('Категория и все её содержимое успешно удалены');
        return back();
    }
}