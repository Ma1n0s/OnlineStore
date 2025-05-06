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

                    TD::make('category_path', 'Категория')
                    ->render(function (Product $product) {
                        if (!$product->category) {
                            return '-';
                        }
                
                        $path = [];
                        $current = $product->category;
                        while ($current) {
                            array_unshift($path, $current);
                            $current = $current->parent;
                        }
                
                        $html = '<div class="d-flex flex-column">';
                        $html .= '<div class="d-flex align-items-center mb-1">';
                        
                        $html .= Link::make($product->category->name)
                            ->route('platform.category.action', $product->category)
                            ->class('font-weight-bold text-decoration-none');
                        
                        $html .= '</div>';
                        
                        if (count($path) > 1) {
                            $html .= '<div class="d-flex align-items-center small text-muted">';
                            $html .= '<span class="mr-1">↳</span>'; 
                            
                            $links = [];
                            foreach ($path as $index => $item) {
                                if ($index === 0) {

                                    $links[] = '<span>'.$item->name.'</span>';
                                } else {
                                    $links[] = Link::make($item->name)
                                        ->route('platform.category.action', $item)
                                        ->class('text-decoration-none');
                                }
                            }
                            
                            $html .= implode(' <span class="mx-1">›</span> ', $links);
                            $html .= '</div>';
                        }
                        
                        $html .= '</div>';
                        return $html;
                    })
                    ->sort(),

                TD::make('price', 'Цена')
                    ->sort()
                    ->width('150px')
                    ->render(function (Product $product) {
                        return '₽' . number_format((float)$product->price, 2);
                    }),

                // TD::make('brand', 'Бренд')
                //     ->sort()
                //     ->filter(Input::make())
                //     ->render(function (Product $product) {
                //         return $product->brand;
                //     }),

                // TD::make('category.name', 'Категория')
                //     ->sort()
                //     ->render(function (Product $product) {
                //         return $product->category 
                //             ? Link::make($product->category->name)
                //                 ->route('platform.category.action', $product->category)
                //             : '-';
                //     }),

                // TD::make('rating', 'Рейтинг')
                //     ->sort()
                //     ->render(function (Product $product) {
                //         return number_format($product->rating, 1);
                //     }),

                // TD::make('created_at', 'Дата создания')
                //     ->sort()
                //     ->render(function (Product $product) {
                //         return $product->created_at->toDateTimeString();
                //     }),

                TD::make('actions', '')
                    ->alignRight()
                    ->width('100px')
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
            ]),
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