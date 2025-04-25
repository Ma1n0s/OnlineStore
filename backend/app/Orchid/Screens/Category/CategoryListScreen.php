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
use Orchid\Attachment\Models\Attachment;

class CategoryListScreen extends Screen
{
    public function query(): array
    {
        return [
            'categories' => Category::with(['parent', 'children'])
                ->orderBy('parent_id')
                ->orderBy('name')
                ->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Категории товаров';
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
            Layout::table('categories', [
                // TD::make('name', 'Путь')
                //     ->render(function (Category $category) {
                //         $indent = str_repeat('&nbsp;&nbsp;', $category->getPath()->count() - 1);
                //         return Link::make($indent . $category->name)
                //             ->route('platform.category.action', $category);
                //     }),
                    
                TD::make('title', 'Заголовок'),
                
                TD::make('parent.name', 'Родительская категория')
                    ->render(function (Category $category) {
                        return $category->parent
                            ? Link::make($category->parent->name)
                                ->route('platform.category.action', $category->parent)
                            : 'Родительская';
                    }),

                // TD::make('children_count', 'Подкатегорий')
                //     ->render(function (Category $category) {
                //         return $category->children_count > 0 
                //             ? "<span class='badge bg-info'>{$category->children_count}</span>"
                //             : "<span class='badge bg-secondary'>{$category->children_count}</span>";
                //     })
                //     ->alignCenter()
                //     ->width('120px'),
                
                TD::make('products_count', 'Товаров')
                    ->render(function (Category $category) {
                        $categoryIds = $category->descendants()->pluck('id')->push($category->id);
                        $count = Product::whereIn('subcategory_id', $categoryIds)->count();
                        return $count > 0 
                            ? "<span class='badge bg-primary'>{$count}</span>"
                            : "<span class='badge bg-secondary'>{$count}</span>";
                }),
                
                TD::make('actions', 'Действия')
                    ->alignRight()
                    ->render(function (Category $category) {
                        return DropDown::make()
                            ->icon('three-dots-vertical')
                            ->list([
                                Link::make('Редактировать')
                                    ->route('platform.category.edit', $category)
                                    ->icon('pencil'),
                                    
                                Link::make('Добавить категорию')
                                    ->route('platform.category.create', ['parent_id' => $category->id])
                                    ->icon('plus'),
                                    
                                Button::make('Удалить')
                                    ->icon('trash')
                                    ->method('removeCategory')
                                    ->confirm('This will delete the category, all its subcategories and products. Are you sure?')
                                    ->parameters(['id' => $category->id]),
                            ]);
                    }),
            ]),
        ];
    }

    public function removeCategory(Request $request)
    {
        $category = Category::findOrFail($request->get('id'));
        
        // Получаем ID всех категорий, которые нужно удалить (текущая + подкатегории)
        $categoryIds = $category->descendants()->pluck('id')->push($category->id);
        
        // Удаляем продукты из категорий (без каскадного удаления)
        Product::whereIn('subcategory_id', $categoryIds)->delete();
        
        // Найдем все категории, которые нужно удалить
        $categoriesToDelete = Category::whereIn('id', $categoryIds)->get();
        
        // Для каждой категории удаляем связанные изображения
        foreach ($categoriesToDelete as $categoryToDelete) {
            // Удаляем все вложения категории, включая изображения
            $categoryToDelete->attachment()->delete();
        }
        
        // Удаляем все подкатегории и саму категорию
        $category->descendants()->delete();
        $category->delete();

        Alert::info('Category and all its contents were deleted successfully');
        return back();
    }
}