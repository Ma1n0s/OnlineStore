<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;

class ProductListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'products' => Product::paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Products';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Create new')
                ->icon('plus')
                ->route('platform.product.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::table('products', [
                TD::make('id', 'ID')
                    ->sort(),

                TD::make('name', 'Name')
                    ->sort()
                    ->filter(Input::make())
                    ->render(function (Product $product) {
                        return Link::make($product->name)
                            ->route('platform.product.edit', $product);
                    }),

                TD::make('code', 'Code')
                    ->sort()
                    ->filter(Input::make()),

                TD::make('price', 'Price')
                    ->sort()
                    ->render(function (Product $product) {
                        return '$' . number_format($product->price, 2);
                    }),

                TD::make('brand', 'Brand')
                    ->sort()
                    ->filter(Input::make()),

                TD::make('category_id', 'Category')
                    ->render(function (Product $product) {
                        return $product->category->name ?? '-';
                    }),

                TD::make('created_at', 'Created')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->created_at->toDateTimeString();
                    }),
            ]),
        ];
    }
}
