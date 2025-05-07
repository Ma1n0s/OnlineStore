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
     */
    public function query(Request $request): array
    {
        return [
            'products' => Product::with('category.parent')
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
     * Display header name.
     */
    public function name(): ?string
    {
        return 'Товары';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Список товаров с категориями и поиском';
    }

    /**
     * The screen's action buttons.
     */
    public function commandBar(): array
    {
        return [
            Link::make('Создать новый')
                ->icon('plus')
                ->route('platform.product.create')
                ->canSee(auth()->user()->hasAccess('platform.products.create')),
        ];
    }

    /**
     * The screen's layout elements.
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('search')
                    ->type('text')
                    ->placeholder('Поиск по названию, артикулу или бренду...')
                    ->value(request()->input('search')),
                    
                Button::make('Поиск')
                    ->icon('magnifier')
                    ->method('performSearch')
                    ->class('btn btn-primary'),
            ]),

            Layout::table('products', [
                TD::make('id', '№')
                    ->width('50px')
                    ->align(TD::ALIGN_CENTER)
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->id;
                    }),
                    
                TD::make('name', 'Название')
                    ->sort()
                    ->filter(Input::make())
                    ->render(function (Product $product) {
                        $html = '<div class="d-flex flex-column">';
                        
                        // Название товара
                        $html .= '<div>' . Link::make($product->name)
                            ->route('platform.product.edit', $product) . '</div>';
                        
                        // Категории (путь)
                        if ($product->category) {
                            $html .= '<div class="w-100 pt-1" style="font-size: 0.875rem; display: flex; align-items: center;">';
                            
                            $path = [];
                            $current = $product->category;
                            while ($current) {
                                array_unshift($path, $current);
                                $current = $current->parent;
                            }
                            
                            $breadcrumbs = [];
                            foreach ($path as $item) {
                                $breadcrumbs[] = Link::make($item->name)
                                    ->route('platform.category.action', $item)
                                    ->class('text-decoration-none');
                            }
                            
                            $html .= implode(' <span class="mx-1">›</span> ', $breadcrumbs);
                            $html .= '</div>';
                        }
                        
                        $html .= '</div>';
                        return $html;
                    }),
                    
                TD::make('price', 'Цена')
                    ->width('150px')
                    ->sort()
                    ->align(TD::ALIGN_RIGHT)
                    ->render(function (Product $product) {
                        return '₽' . number_format((float)$product->price, 2);
                    }),
                    
                TD::make('actions', '')
                    ->width('100px')
                    ->alignRight()
                    ->render(function (Product $product) {
                        return DropDown::make()
                            ->icon('three-dots-vertical')
                            ->list([
                                Link::make('Редактировать')
                                    ->route('platform.product.edit', $product)
                                    ->icon('pencil'),
                                    
                                Button::make('Удалить')
                                    ->icon('trash')
                                    ->method('remove')
                                    ->confirm('Удалить этот товар?')
                                    ->parameters(['id' => $product->id]),
                            ]);
                    }),
            ])->title('Список товаров'),
        ];
    }

    /**
     * Perform search action.
     */
    public function performSearch(Request $request)
    {
        return redirect()->route('platform.product.list', [
            'search' => $request->input('search')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function remove(Request $request)
    {
        $product = Product::findOrFail($request->get('id'));
        $product->delete();
        Alert::info('Товар удален');
        return back();
    }
}