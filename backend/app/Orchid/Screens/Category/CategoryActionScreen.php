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
            'children' => $category->children()->orderBy('name')->paginate(10),
            'products' => Product::whereIn('subcategory_id', $categoryIds)
                ->with('subcategory')
                ->orderBy('name')
                ->paginate(10),
        ];
    }

    public function name(): ?string
    {
        return $this->category->exists 
            ? "Управление: {$this->category->name}" 
            : 'Новая категория';
    }

    public function description(): ?string
    {
        return $this->category->exists
            ? "Просмотр и управление категорией {$this->category->name}"
            : 'Создание новой категории';
    }

    public function commandBar(): array
    {
        $canAddSubcategory = $this->category->canHaveSubcategories();
        $canAddProduct = $this->category->canHaveProducts();
        
        return [
            Link::make('Назад')
                ->icon('arrow-left')
                ->route('platform.category.list'),
                
            Link::make('Добавить подкатегорию')
                ->icon('plus')
                ->route('platform.category.create', ['parent_id' => $this->category->id])
                ->canSee($canAddSubcategory)
                ->class('btn btn-primary'),
                
            Link::make('Добавить товар')
                ->icon('bag')
                ->route('platform.product.create', ['category_id' => $this->category->id])
                ->canSee($canAddProduct)
                ->class('btn btn-success'),
                
            Link::make('Редактировать')
                ->icon('pencil')
                ->route('platform.category.edit', $this->category)
                ->class('btn btn-info'),
                
            Button::make('Удалить')
                ->icon('trash')
                ->method('removeCategory')
                ->confirm('Вы уверены? Категория, все подкатегории и товары будут удалены безвозвратно!')
                ->class('btn btn-danger')
                ->canSee($this->category->exists),
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
                        return Link::make($category->name)
                            ->route('platform.category.action', $category)
                            ->icon('folder');
                    }),
                    
                TD::make('title', 'Заголовок'),
                
                TD::make('products_count', 'Товаров')
                    ->render(function (Category $category) {
                        $count = Product::where('subcategory_id', $category->id)->count();
                        return $count > 0 
                            ? "<span class='badge bg-primary'>$count</span>" 
                            : "<span class='badge bg-secondary'>0</span>";
                    }),
                
                TD::make('updated_at', 'Обновлено')
                    ->render(function (Category $category) {
                        return $category->updated_at->format('d.m.Y H:i');
                    }),
                
                TD::make('actions', 'Действия')
                    ->alignRight()
                    ->width('100px')
                    ->render(function (Category $category) {
                        return DropDown::make()
                            ->icon('three-dots-vertical')
                            ->list([
                                Link::make('Редактировать')
                                    ->route('platform.category.edit', $category)
                                    ->icon('pencil'),
                                    
                                Link::make('Добавить подкатегорию')
                                    ->route('platform.category.create', ['parent_id' => $category->id])
                                    ->icon('plus')
                                    ->canSee($category->canHaveSubcategories()),
                                    
                                Button::make('Удалить')
                                    ->icon('trash')
                                    ->method('removeCategory')
                                    ->confirm('Удалить подкатегорию и все товары?')
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
                            ->route('platform.product.edit', $product)
                            ->icon('bag');
                    }),
                    
                TD::make('code', 'Код'),
                
                TD::make('price', 'Цена')
                    ->render(function (Product $product) {
                        return number_format($product->price, 2) . ' ₽';
                    }),
                
                TD::make('brand', 'Бренд'),
                
                TD::make('rating', 'Рейтинг')
                    ->render(function (Product $product) {
                        $stars = str_repeat('★', floor($product->rating)) . 
                                 str_repeat('☆', 5 - floor($product->rating));
                        return "<span title='{$product->rating}'>{$stars}</span>";
                    }),
                
                TD::make('updated_at', 'Обновлено')
                    ->render(function (Product $product) {
                        return $product->updated_at->format('d.m.Y H:i');
                    }),
                
                TD::make('actions', 'Действия')
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
                                    ->method('removeProduct')
                                    ->confirm('Удалить товар?')
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
        
        // Get all category IDs to delete
        $categoryIds = $category->descendants()->pluck('id')->push($category->id);
        
        // Delete all products and their images
        $products = Product::whereIn('subcategory_id', $categoryIds)->get();
        
        foreach ($products as $product) {
            $this->deleteProductImages($product);
            $product->delete();
        }
        
        // Delete all subcategories and their images
        $descendants = $category->descendants()->get();
        foreach ($descendants as $descendant) {
            $this->deleteCategoryImages($descendant);
            $descendant->delete();
        }
        
        // Delete the category's images
        $this->deleteCategoryImages($category);
        
        // Delete the category itself
        $category->delete();

        Alert::success('Категория и все её содержимое успешно удалено');
        
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
        
        $this->deleteProductImages($product);
        $product->delete();

        Alert::success('Товар успешно удален');
        return redirect()->route('platform.category.action', $request->get('category_id'));
    }
}