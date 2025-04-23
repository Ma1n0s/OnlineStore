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
use Illuminate\Http\Request;
use Orchid\Support\Facades\Alert;
use Illuminate\Support\Facades\Storage;

class CategoryActionScreen extends Screen
{
    public $category;

    public function query(Category $category): array
    {
        $categoryIds = $category->descendants()->pluck('id')->push($category->id);
        
        return [
            'category' => $category,
            'children' => $category->children()->paginate(10),
            'products' => Product::whereIn('subcategory_id', $categoryIds)
                ->with('subcategory')
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
        return [
            Link::make('Назад')
                ->icon('arrow-left')
                ->route('platform.category.list'),
                
            Link::make('Добавить подкатегорию')
                ->icon('folder-plus')
                ->route('platform.category.create', ['parent_id' => $this->category->id]),
                
            Link::make('Добавить товар')
                ->icon('bag')
                ->route('platform.product.create', ['category_id' => $this->category->id]),
                
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

        if ($this->category->children()->exists()) {
            $layouts[] = Layout::table('children', [
                TD::make('name', 'Название')
                    ->width('300px')
                    ->render(function (Category $category) {
                        $currentDepth = $this->category->exists ? $this->category->depth : 0;
                        $indentLevel = max(0, $category->depth - $currentDepth - 1);
                        $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $indentLevel);
                        $icon = $category->children()->exists() ? '<i class="icon-folder"></i> ' : '<i class="icon-folder-alt"></i> ';
                        return Link::make($icon . $indent . $category->name)
                            ->route('platform.category.action', $category);
                    })
                    ->sort(),
                    
                TD::make('title', 'Заголовок')
                    ->width('200px'),
                
                TD::make('products_count', 'Товаров')
                    ->render(function (Category $category) {
                        $count = Product::where('subcategory_id', $category->id)->count();
                        return $count > 0 
                            ? "<span class='badge bg-primary'>{$count}</span>"
                            : "<span class='badge bg-secondary'>{$count}</span>";
                    })
                    ->alignCenter()
                    ->width('100px'),
                
                TD::make('actions', 'Действия')
                    ->alignRight()
                    ->width('100px')
                    ->render(function (Category $category) {
                        return DropDown::make()
                            ->icon('options-vertical')
                            ->list([
                                Link::make('Редактировать')
                                    ->route('platform.category.edit', $category)
                                    ->icon('pencil'),
                                    
                                Link::make('Добавить подкатегорию')
                                    ->route('platform.category.create', ['parent_id' => $category->id])
                                    ->icon('folder-plus'),
                                    
                                Button::make('Удалить')
                                    ->icon('trash')
                                    ->method('removeCategory')
                                    ->confirm('Будет удалена подкатегория и все её товары. Вы уверены?')
                                    ->parameters(['id' => $category->id]),
                            ]);
                    }),
            ]);
        }

        if ($this->category->canHaveProducts()) {
            $layouts[] = Layout::table('products', [
                TD::make('name', 'Название')
                    ->width('300px')
                    ->render(function (Product $product) {
                        return Link::make($product->name)
                            ->route('platform.product.edit', $product);
                    })
                    ->sort(),
                    
                TD::make('code', 'Артикул')
                    ->width('150px'),
                
                TD::make('price', 'Цена')
                    ->render(function (Product $product) {
                        return number_format($product->price, 2) . ' ₽';
                    })
                    ->alignRight()
                    ->width('100px'),
                
                TD::make('brand', 'Бренд')
                    ->width('150px'),
                
                TD::make('rating', 'Рейтинг')
                    ->render(function (Product $product) {
                        $rating = number_format($product->rating, 1);
                        $stars = str_repeat('★', floor($product->rating)) . 
                                (fmod($product->rating, 1) >= 0.5 ? '½' : '');
                        return "<span title='{$rating}'>{$stars}</span>";
                    })
                    ->alignCenter()
                    ->width('100px'),
                
                TD::make('actions', 'Действия')
                    ->alignRight()
                    ->width('100px')
                    ->render(function (Product $product) {
                        return DropDown::make()
                            ->icon('options-vertical')
                            ->list([
                                Link::make('Редактировать')
                                    ->route('platform.product.edit', $product)
                                    ->icon('pencil'),
                                    
                                Button::make('Удалить')
                                    ->icon('trash')
                                    ->method('removeProduct')
                                    ->confirm('Вы уверены, что хотите удалить этот товар?')
                                    ->parameters([
                                        'product_id' => $product->id,
                                        'category_id' => $this->category->id,
                                    ]),
                            ]);
                    }),
            ]);
        }

        return $layouts;
    }

    public function removeCategory(Request $request)
    {
        $category = Category::findOrFail($request->get('id'));
        
        // Получаем все ID категорий для удаления (включая подкатегории)
        $categoryIds = $category->descendants()->pluck('id')->push($category->id);
        
        // Удаляем все товары и их изображения
        $products = Product::whereIn('subcategory_id', $categoryIds)->get();
        
        foreach ($products as $product) {
            $this->deleteProductImages($product);
            $product->delete();
        }
        
        // Удаляем все подкатегории и их изображения
        $descendants = $category->descendants()->get();
        foreach ($descendants as $descendant) {
            $this->deleteCategoryImages($descendant);
            $descendant->delete();
        }
        
        // Удаляем изображения самой категории
        $this->deleteCategoryImages($category);
        
        // Удаляем саму категорию
        $category->delete();

        Alert::success('Категория и все её содержимое успешно удалены');
        
        if ($category->parent_id) {
            return redirect()->route('platform.category.action', $category->parent);
        }
        
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
        
        // Удаляем изображения товара
        $this->deleteProductImages($product);
        
        $product->delete();

        Alert::success('Товар успешно удален');
        return redirect()->route('platform.category.action', $request->get('category_id'));
    }
}