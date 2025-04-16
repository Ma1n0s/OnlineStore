<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;

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
            'products' => Product::with('subcategory')
                ->filters()
                ->defaultSort('id', 'desc')
                ->paginate(),
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
                ->route('platform.product.create')
                ->canSee(auth()->user()->hasAccess('platform.products.create')),
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

                TD::make('subcategory.name', 'Subcategory')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->subcategory->name ?? '-';
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

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Request $request)
    {
        $product = Product::findOrFail($request->get('id'));
        
        // Delete associated images
        $product->images()->delete();
        
        $product->delete();

        Alert::info('Product was deleted');

        return redirect()->route('platform.product.list');
    }
}