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
                ->paginate(10),
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
                    ->width('70px')
                    ->align(TD::ALIGN_CENTER)
                    ->sort()
                    ->style('white-space: nowrap;')
                    ->render(function (Product $product) {
                        return $product->id ? number_format($product->id, 0, '', '') : '-';
                    }),

                TD::make('type', 'Тип')
                    ->width('100px')
                    ->sort()
                    ->render(function (Product $product) {
                        $types = [
                            'preorder' => 'Под заказ',
                            'rent' => 'Аренда',
                            'instock' => 'В наличии',
                        ];
                        return $types[$product->type ?? ''] ?? ($product->type ?? '-');
                    }),

                TD::make('delivery_days', 'Доставка')
                    ->width('90px')
                    ->sort()
                    ->align(TD::ALIGN_CENTER)
                    ->render(function (Product $product) {
                        return ($product->type === 'preorder' && $product->delivery_days) 
                            ? $product->delivery_days . ' дн.' 
                            : '-';
                    }),
                    
                TD::make('name', 'Название')
                    ->sort()
                    ->filter(Input::make())
                    ->render(function (Product $product) {
                        $html = '<div class="d-flex flex-column" style="min-width: 250px;">';
                        
                        $name = $product->name ?? 'Без названия';
                        $displayName = mb_strlen($name) > 50 ? mb_substr($name, 0, 50) . '...' : $name;
                        
                        if ($product->exists && $product->id) {
                            try {
                                $editUrl = route('platform.product.edit', ['product' => $product->id]);
                                $html .= '<div class="text-truncate"><a href="'.e($editUrl).'">'.e($displayName).'</a></div>';
                            } catch (\Exception $e) {
                                $html .= '<div class="text-truncate">'.e($displayName).'</div>';
                                \Log::error('Route error for product '.$product->id.': '.$e->getMessage());
                            }
                        } else {
                            $html .= '<div class="text-truncate">'.e($displayName).'</div>';
                        }
                        
                        // if ($product->category) {
                        //     $html .= $this->renderCategoryPath($product->category);
                        // }
                        
                        $html .= '</div>';
                        return $html;
                    }),
                    
                TD::make('price', 'Цена')
                    ->width('120px')
                    ->sort()
                    ->align(TD::ALIGN_RIGHT)
                    ->render(function (Product $product) {
                        return isset($product->price) 
                            ? number_format((float)$product->price, 2) . ' ₽' 
                            : '0.00 ₽';
                    }),

                TD::make('quantity', 'Кол-во')
                    ->width('80px')
                    ->sort()
                    ->align(TD::ALIGN_CENTER)
                    ->render(function (Product $product) {
                        return $product->quantity ?? 0;
                    }),
                    
                TD::make('actions', '')
                    ->width('80px')
                    ->align(TD::ALIGN_CENTER)
                    ->render(function (Product $product) {
                        if (!$product->exists || !$product->id) {
                            return '';
                        }
                        
                        return DropDown::make()
                            ->icon('three-dots-vertical')
                            ->list([
                                Link::make('Редактировать')
                                    ->route('platform.product.edit', $product->id)
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

    protected function renderCategoryPath($category)
{
    $html = '<div class="w-100 pt-1" style="font-size: 0.75rem; color: #6c757d; display: flex; align-items: center;">';
    
    $path = [];
    $current = $category;
    while ($current) {
        if ($current->name) {
            array_unshift($path, $current);
        }
        $current = $current->parent ?? null;
    }
    
    $breadcrumbs = [];
    foreach ($path as $item) {
        if ($item->id && $item->name) {
            try {
                $url = route('platform.category.action', ['category' => $item->id]);
                $breadcrumbs[] = '<a href="'.e($url).'" class="text-decoration-none">'.e($item->name).'</a>';
            } catch (\Exception $e) {
                $breadcrumbs[] = e($item->name);
                \Log::error('Route error for category '.$item->id.': '.$e->getMessage());
            }
        }
    }
    
    if (!empty($breadcrumbs)) {
        $html .= implode(' <span class="mx-1">›</span> ', $breadcrumbs);
    }
    $html .= '</div>';
    
    return $html;
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