<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Picture;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class ProductScreen extends Screen
{
    /**
     * @var Product
     */
    public $product;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Product $product): array
    {
        $product->load('subcategory','images');
        $categoryId = request()->input('category_id');
        
        return [
            'product' => $product,
            'images' => $product->images,
            'category_id' => $categoryId,
            'subcategories' => $categoryId 
                ? Category::where('parent_id', $categoryId)->get()
                : collect(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->product->exists ? 'Edit Product' : 'Create Product';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Button::make('Create')
                ->icon('plus')
                ->method('createOrUpdate')
                ->canSee(!$this->product->exists),

            Button::make('Update')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->product->exists),

            Button::make('Remove')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->product->exists),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        $categoryId = request()->input('category_id');
        $isCreatingInCategory = !$this->product->exists && $categoryId;
        
        return [
            Layout::rows([
                Input::make('product.code')
                    ->title('Code')
                    ->required(),

                Input::make('product.name')
                    ->title('Name')
                    ->required(),

                TextArea::make('product.description')
                    ->title('Description')
                    ->rows(3),

                Input::make('product.price')
                    ->title('Price')
                    ->type('number')
                    ->step('0.01')
                    ->required(),

                Input::make('product.article')
                    ->title('Article')
                    ->required(),

                Input::make('product.brand')
                    ->title('Brand')
                    ->required(),

                Input::make('product.rating')
                    ->title('Rating')
                    ->type('number')
                    ->step('0.1')
                    ->min(0)
                    ->max(5),

                // Поле для выбора подкатегории
                $isCreatingInCategory
                    ? Input::make('product.subcategory_id')
                        ->title('Subcategory')
                        ->value($this->getDefaultSubcategoryId())
                        ->readonly()
                        ->help('This product will be added to: ' . $this->getSubcategoryName())
                    : Select::make('product.subcategory_id')
                        ->title('Subcategory')
                        ->fromModel(Category::class, 'name')
                        ->empty('No subcategory'),

                Matrix::make('product.specifications')
                    ->title('Specifications')
                    ->columns([
                        'Key',
                        'Value',
                    ])
                    ->fields([
                        'Key' => Input::make(),
                        'Value' => Input::make(),
                    ]),

                Upload::make('images')
                    ->title('Product Images')
                    ->multiple()
                    ->maxFiles(10)
                    ->acceptedFiles('image/*'),

                Input::make('product.warranty')
                    ->title('Warranty'),

                Matrix::make('product.advantages')
                    ->title('Advantages')
                    ->columns([
                        'Title',
                        'Description',
                    ])
                    ->fields([
                        'Title' => Input::make(),
                        'Description' => Input::make(),
                    ]),

                Matrix::make('product.specificationsB')
                    ->title('Additional Specifications')
                    ->columns([
                        'Name',
                        'Value',
                    ])
                    ->fields([
                        'Name' => Input::make(),
                        'Value' => Input::make(),
                    ]),
            ]),
        ];
    }

    /**
     * Get default subcategory ID when creating product in a category
     */
    protected function getDefaultSubcategoryId()
    {
        $categoryId = request()->input('category_id');
        if ($categoryId) {
            $subcategories = Category::where('parent_id', $categoryId)->get();
            if ($subcategories->isNotEmpty()) {
                return $subcategories->first()->id;
            }
        }
        return null;
    }

    /**
     * Get subcategory name for help text
     */
    protected function getSubcategoryName()
    {
        $subcategoryId = $this->getDefaultSubcategoryId();
        if ($subcategoryId) {
            return Category::find($subcategoryId)->name;
        }
        return 'No subcategory selected';
    }

    /**
     * @param Product $product
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Product $product, Request $request)
    {
        $data = $request->get('product');

        // Если создается продукт в категории, устанавливаем subcategory_id
        if (!$product->exists && $request->has('category_id')) {
            $categoryId = $request->input('category_id');
            $subcategories = Category::where('parent_id', $categoryId)->get();
            
            if ($subcategories->isNotEmpty()) {
                $data['subcategory_id'] = $subcategories->first()->id;
                $data['category_id'] = $categoryId;
            }
        }

        $data['rating'] = $data['rating'] ?? 0;
        $data['specifications'] = $data['specifications'] ?? [];
        $data['advantages'] = $data['advantages'] ?? [];
        $data['specificationsB'] = $data['specificationsB'] ?? [];
        $data['images'] = [];
        
        if ($request->has('images')) {
            foreach ($request->input('images', []) as $imageId) {
                $attachment = Attachment::find($imageId);
                
                if ($attachment) {
                    $data['images'][] = [
                        'url' => $attachment->url,
                        'path' => str_replace('public/', '', $attachment->physicalPath()),
                        'name' => $attachment->name,
                        'original_name' => $attachment->original_name,
                        'mime_type' => $attachment->mime_type,
                        'size' => $attachment->size,
                    ];
                }
            }
        }
        
        $product->fill($data)->save();
        
        Alert::info('Product was saved');
        
        // Перенаправляем на список продуктов в категории, если создавали из категории
        if ($request->has('category_id')) {
            return redirect()->route('platform.category.action', $request->input('category_id'));
        }
        
        return redirect()->route('platform.product.list');
    }

    /**
     * @param Product $product
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Product $product)
    {
        if (!empty($product->images)) {
            foreach ($product->images as $image) {
                if (isset($image['path'])) {
                    Storage::disk('public')->delete($image['path']);
                }
            }
        }
        
        $product->delete();

        Alert::info('Product was removed');
        return redirect()->route('platform.product.list');
    }
}