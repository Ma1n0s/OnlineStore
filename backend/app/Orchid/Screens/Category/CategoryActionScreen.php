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

class CategoryActionScreen extends Screen
{
    public $category;

    public function query(Category $category): array
    {
        $categoryIds = $category->descendants()->pluck('id')->push($category->id);
        
        return [
            'category' => $category,
            'children' => $category->children()->paginate(10),
            'products' => Product::whereIn('subcategory_id', $categoryIds)->paginate(10),
        ];
    }

    public function name(): ?string
    {
        return "Manage: {$this->category->name}";
    }

    public function commandBar(): array
    {
        $canAddSubcategory = $this->category->canHaveSubcategories();
        $canAddProduct = $this->category->canHaveProducts();
        
        return [
            Link::make('Back')
                ->icon('arrow-left')
                ->route('platform.category.list'),
                
            Link::make('Add Subcategory')
                ->icon('plus')
                ->route('platform.category.create', ['parent_id' => $this->category->id])
                ->canSee($canAddSubcategory),
                
            Link::make('Add Product')
                ->icon('bag')
                ->route('platform.product.create', ['category_id' => $this->category->id])
                ->canSee($canAddProduct),
                
            Link::make('Edit')
                ->icon('pencil')
                ->route('platform.category.edit', $this->category),
                
            Button::make('Delete')
                ->icon('trash')
                ->method('removeCategory')
                ->confirm('This will delete the category, all its subcategories and products. Are you sure?')
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
                TD::make('name', 'Name')
                    ->render(function (Category $category) {
                        return Link::make($category->name)
                            ->route('platform.category.action', $category);
                    }),
                    
                TD::make('title', 'Title'),
                
                TD::make('actions', 'Actions')
                    ->alignRight()
                    ->render(function (Category $category) {
                        return DropDown::make()
                            ->icon('three-dots-vertical')
                            ->list([
                                Link::make('Edit')
                                    ->route('platform.category.edit', $category)
                                    ->icon('pencil'),
                                    
                                Button::make('Delete')
                                    ->icon('trash')
                                    ->method('removeCategory')
                                    ->confirm('This will delete the subcategory and all its products. Are you sure?')
                                    ->parameters(['id' => $category->id]),
                            ]);
                    }),
            ]);
        }

        if ($this->category->canHaveProducts()) {
            $layouts[] = Layout::table('products', [
                TD::make('name', 'Name')
                    ->render(function (Product $product) {
                        return Link::make($product->name)
                            ->route('platform.product.edit', $product);
                    }),
                    
                TD::make('code', 'Code'),
                
                TD::make('price', 'Price')
                    ->render(function (Product $product) {
                        return '$' . number_format($product->price, 2);
                    }),
                
                TD::make('brand', 'Brand'),
                
                TD::make('rating', 'Rating')
                    ->render(function (Product $product) {
                        return number_format($product->rating, 1);
                    }),
                
                TD::make('actions', 'Actions')
                    ->alignRight()
                    ->render(function (Product $product) {
                        return DropDown::make()
                            ->icon('three-dots-vertical')
                            ->list([
                                Link::make('Edit')
                                    ->route('platform.product.edit', $product)
                                    ->icon('pencil'),
                                    
                                Button::make('Delete')
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
        }

        return $layouts;
    }

    public function removeCategory(Request $request)
    {
        $category = Category::findOrFail($request->get('id'));
        
        $categoryIds = $category->descendants()->pluck('id')->push($category->id);
        
        $products = Product::whereIn('subcategory_id', $categoryIds)->get();
        
        foreach ($products as $product) {
            $this->deleteProductImages($product);
            $product->delete();
        }
        
        $category->descendants()->delete();
        
        $this->deleteCategoryImages($category);
        
        $category->delete();

        Alert::info('Category and all its contents were deleted successfully');
        
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

        Alert::info('Product was deleted');
        return redirect()->route('platform.category.action', $request->get('category_id'));
    }
}