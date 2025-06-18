<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use App\Models\Product;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Fields\Input;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Alert;
use Illuminate\Support\Facades\Storage;

class CategoryActionScreen extends Screen
{
    public $category;

    public function query(Category $category): array
    {
        return [
            'category' => $category,
            'children' => $category->children()->paginate(10),
            'products' => Product::where('category_id', $category->id)
                ->with('category')
                ->paginate(10),
        ];
    }

    public function name(): ?string
    {
        return $this->category->exists 
            ? "Управление категорией: {$this->category->name}" 
            : "Новая категория";
    }

    public function description(): ?string
    {
        return $this->category->exists 
            ? "Просмотр и управление категорией и её содержимым"
            : "Создание новой категории";
    }

    public function commandBar(): array
    {
        $hasProducts = Product::where('category_id', $this->category->id)->exists();
        $hasSubcategories = $this->category->children()->exists();
        
        return [
            Link::make('Назад')
                ->icon('arrow-left')
                ->route('platform.category.list'),
                
            Link::make('Добавить подкатегорию')
                ->icon('folder-plus')
                ->route('platform.category.create', ['parent_id' => $this->category->id])
                ->canSee(!$hasProducts),
                
            Link::make('Добавить товар')
                ->icon('bag')
                ->route('platform.product.create', ['category_id' => $this->category->id])
                ->canSee(!$hasSubcategories),

            Link::make('Редактировать')
                ->icon('pencil')
                ->route('platform.category.edit', $this->category),
                
            Button::make('Удалить')
                ->icon('trash')
                ->method('removeCategory')
                ->confirm('Будет удалена категория, все подкатегории и товары. Вы уверены?')
                ->canSee($this->category->exists)
                ->parameters(['id' => $this->category->id]),
        ];
    }
    
    public function layout(): array
    {
        $layouts = [
            Layout::view('platform.category.actions', [
                'category' => $this->category,
            ]),
        ];

        $layouts[] = Layout::table('children', [
            TD::make('name', 'Название')
                ->width('300px')
                ->render(function (Category $category) {
                    $currentDepth = $this->category->exists ? $this->category->depth : 0;
                    $indentLevel = max(0, $category->depth - $currentDepth - 1);
                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $indentLevel);
                    $icon = $category->children()->exists() ? '' : '';
                    
                    $name = $category->name;
                    $displayName = mb_strlen($name) > 50 ? mb_substr($name, 0, 50) . '...' : $name;
                    
                    return Link::make($icon . $indent . e($displayName))
                        ->route('platform.category.action', $category);
                })
                ->sort(),
                
            TD::make('title', 'Заголовок')
                ->width('200px')
                ->render(function (Category $category) {
                    $title = $category->title ?? '';
                    return mb_strlen($title) > 30 ? mb_substr($title, 0, 30) . '...' : $title;
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
                                
                            Button::make('Удалить')
                                ->icon('trash')
                                ->method('removeCategory')
                                ->confirm('This will delete the subcategory and all its products. Are you sure?')
                                ->parameters(['id' => $category->id]),
                        ]);
                }),
        ]);

        $layouts[] = Layout::table('products', [
            TD::make('id', 'ID')
                ->sort()
                ->render(function (Product $product) {
                    return $product->id;
                }),

            TD::make('name', 'Название')
                ->sort()
                ->filter(Input::make())
                ->render(function (Product $product) {
                    $name = $product->name ?? 'Без названия';
                    $displayName = mb_strlen($name) > 50 ? mb_substr($name, 0, 50) . '...' : $name;
                    
                    return Link::make(e($displayName))
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
                    return number_format((float)$product->price, 2) . ' ₽';
                }),

            TD::make('category.name', 'Подкатегория')
                ->sort()
                ->render(function (Product $product) {
                    return $product->category ? e($product->category->name) : '-'; 
                }),
            
            TD::make('actions', 'Действия')
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
                                ->method('removeProduct')
                                ->confirm('Are you sure you want to delete this product?')
                                ->parameters([
                                    'product_id' => $product->id,
                                    'category_id' => $this->category->id,
                                ]),
                        ]);
                }),
        ]);

        return $layouts;
    }

    public function removeCategory(Request $request)
    {
        $category = Category::findOrFail($request->get('id'));

        if ($category->products()->exists()) {
            Alert::error('Нельзя удалить категорию, в которой есть товары');
            return back();
        }

        $categoriesToDelete = $category->descendants()->with('products')->get();

        foreach ($categoriesToDelete as $subCategory) {
            if ($subCategory->products()->exists()) {
                Alert::error('Нельзя удалить категорию, так как в её подкатегориях есть товары');
                return back();
            }
        }

        $this->deleteCategoryImages($category);
        foreach ($categoriesToDelete as $subCategory) {
            $this->deleteCategoryImages($subCategory);
        }

        foreach ($categoriesToDelete as $subCategory) {
            $subCategory->products()->delete(); 
            $subCategory->delete();
        }

        $category->delete();

        Alert::info('Категория и все её подкатегории успешно удалены');
        return redirect()->route('platform.category.list');
    }

    protected function deleteCategoryImages(Category $category)
    {
        if ($category->image_url) {
            Storage::disk('public')->delete($category->image_url);
        }
        if ($category->description_image_url) {
            Storage::disk('public')->delete($category->description_image_url);
        }
    }

    protected function deleteProductImages(Product $product)
    {
        if (!empty($product->images)) {
            foreach ($product->images as $image) {
                if (isset($image['path'])) {
                    Storage::disk('public')->delete($image['path']);
                }
            }
        }
    }

    public function removeProduct(Request $request)
    {
        $product = Product::findOrFail($request->get('product_id'));
        
        $this->deleteProductImages($product);
        
        $product->delete();

        Alert::success('Товар успешно удален');
        return redirect()->route('platform.category.action', $request->get('category_id'));
    }
}