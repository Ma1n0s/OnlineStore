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
     * @param Request $request
     * @return array
     */
    public function query(Request $request): array
    {
        return [
            'products' => Product::with('category')
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('code', 'LIKE', "%{$search}%")
                          ->orWhere('article', 'LIKE', "%{$search}%")
                          ->orWhere('brand', 'LIKE', "%{$search}%")
                          ->orWhereHas('category', function($q) use ($search) {
                              $q->where('name', 'LIKE', "%{$search}%");
                          });
                })
                ->filters()
                ->defaultSort('id', 'desc')
                ->paginate(),
            'search' => $request->input('search')
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Товары';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Список товаров с возможностью поиска';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Создать новый')
                ->icon('plus')
                ->route('platform.product.create')
                ->canSee(auth()->user()->hasAccess('platform.products.create')),

            Button::make('Экспорт')
                ->icon('cloud-download')
                ->method('export')
                ->canSee(auth()->user()->hasAccess('platform.products.export')),
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
            Layout::rows([
                Input::make('search')
                    ->type('text')
                    ->placeholder('Поиск по названию')
                    ->value(request()->input('search')),
                    
                Button::make('Поиск')
                    ->icon('magnifier')
                    ->method('performSearch')
                    ->class('btn btn-primary'),
            ]),

            Layout::table('products', [
                TD::make('id', 'ID')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->id;
                    }),

                TD::make('name', 'Название')
                    ->sort()
                    ->filter(Input::make())
                    ->render(function (Product $product) {
                        return Link::make($product->name)
                            ->route('platform.product.edit', $product);
                    }),

                TD::make('code', 'Артикул')
                    ->sort()
                    ->filter(Input::make())
                    ->render(function (Product $product) {
                        return $product->code;
                    }),

                TD::make('price', 'Цена')
                    ->sort()
                    ->render(function (Product $product) {
                        return '₽' . number_format((float)$product->price, 2);
                    }),

                TD::make('brand', 'Бренд')
                    ->sort()
                    ->filter(Input::make())
                    ->render(function (Product $product) {
                        return $product->brand;
                    }),

                TD::make('category.name', 'Категория')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->category 
                            ? Link::make($product->category->name)
                                ->route('platform.category.action', $product->category)
                            : '-';
                    }),

                TD::make('rating', 'Рейтинг')
                    ->sort()
                    ->render(function (Product $product) {
                        return number_format($product->rating, 1);
                    }),

                TD::make('created_at', 'Дата создания')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->created_at->toDateTimeString();
                    }),

                TD::make('actions', 'Действия')
                    ->alignRight()
                    ->render(function (Product $product) {
                        return DropDown::make()
                            ->icon('three-dots-vertical')
                            ->list([
                                Link::make('Редактировать')
                                    ->route('platform.product.edit', $product)
                                    ->icon('pencil')
                                    ->canSee(auth()->user()->hasAccess('platform.products.edit')),
                                    
                                Button::make('Удалить')
                                    ->icon('trash')
                                    ->method('remove')
                                    ->confirm('Вы уверены, что хотите удалить этот товар?')
                                    ->parameters(['id' => $product->id])
                                    ->canSee(auth()->user()->hasAccess('platform.products.delete')),
                            ]);
                    }),
            ]),
        ];
    }

    /**
     * Perform search action.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function performSearch(Request $request)
    {
        return redirect()->route('platform.product.list', [
            'search' => $request->input('search')
        ]);
    }

    /**
     * Export products
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        // Реализация экспорта
        return response()->download(storage_path('exports/products.csv'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Request $request)
    {
        $product = Product::findOrFail($request->get('id'));
        
        $product->images()->delete();
        $product->delete();

        Alert::info('Товар был удален');
        return redirect()->route('platform.product.list');
    }
}