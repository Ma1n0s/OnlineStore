<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use App\Models\Product;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;

class CategoryListScreen extends Screen
{
    /**
     * Query data.
     */
    public function query(Request $request): array
    {
        return [
            'categories' => Category::with(['parent', 'children'])
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('slug', 'LIKE', "%{$search}%");
                })
                ->orderBy('parent_id')
                ->orderBy('name')
                ->paginate(),
        ];
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return 'Категории товаров';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Полный список категорий с поиском';
    }

    /**
     * Button commands.
     */
    public function commandBar(): array
    {
        return [
            Link::make('Добавить корневую категорию')
                ->icon('plus')
                ->route('platform.category.create'),
        ];
    }

    /**
     * Views.
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
            
            Layout::table('categories', [
                TD::make('category_info', 'Категория')
                    ->render(function (Category $category) {
                        $path = [];
                        $current = $category;
                        while ($current) {
                            array_unshift($path, $current);
                            $current = $current->parent;
                        }

                        $html = '<div class="d-flex flex-column">';
                        $html .= '<div class="d-flex align-items-center mb-1">';
                        $html .= Link::make($category->name)
                            ->route('platform.category.action', $category)
                            ->class('font-weight-bold text-decoration-none');
                        // $html .= '<span class="text-muted small ml-2">('.$category->slug.')</span>'; //url поле 
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

                // TD::make('products_count', 'Товаров')
                //     ->render(function (Category $category) {
                //         $count = Product::where('category_id', $category->id)->count();
                //         return $count > 0 
                //             ? "<span class='badge bg-primary'>{$count}</span>"
                //             : "<span class='badge bg-secondary'>{$count}</span>";
                //     })
                //     ->alignRight(),

                TD::make('actions', 'Действия')
                    ->alignRight()
                    ->render(function (Category $category) {
                        return DropDown::make()
                            ->icon('three-dots-vertical')
                            ->list([
                                Link::make('Редактировать')
                                    ->route('platform.category.edit', $category)
                                    ->icon('pencil'),
                                    
                                Link::make('Добавить подкатегорию')
                                    ->route('platform.category.create', ['parent_id' => $category->id])
                                    ->icon('plus'),
                                    
                                Button::make('Удалить')
                                    ->icon('trash')
                                    ->method('removeCategory')
                                    ->confirm('Будет удалена категория, все подкатегории и товары. Вы уверены?')
                                    ->parameters(['id' => $category->id]),
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
        return redirect()->route('platform.category.list', [
            'search' => $request->input('search')
        ]);
    }

    /**
     * Remove category.
     */
    public function removeCategory(Request $request)
    {
        $category = Category::findOrFail($request->get('id'));
        
        $categoryIds = $category->descendants()->pluck('id')->push($category->id);
        Product::whereIn('category_id', $categoryIds)->delete();
        
        $categoriesToDelete = Category::whereIn('id', $categoryIds)->get();
        foreach ($categoriesToDelete as $categoryToDelete) {
            $categoryToDelete->attachment()->delete();
        }
        
        $category->descendants()->delete();
        $category->delete();

        Alert::info('Категория и все её содержимое успешно удалены');
        return back();
    }
}