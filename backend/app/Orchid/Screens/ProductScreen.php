<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use App\Models\Category;
use App\Models\Image;
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
        $product->load('subcategory', 'attachments');
        $categoryId = request()->input('category_id');
        
        return [
            'product' => $product,
            'specifications' => $product->specifications ?? [],
            'specificationsB' => $product->specificationsB ?? [],
            'advantages' => $product->advantages ?? [],
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
        return $this->product->exists ? "Редактирование товара: {$this->product->name}" : 'Создание нового товара';
    }

    /**
     * The description of the screen displayed in the header.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return "Управление товарами магазина. Заполните все необходимые поля для создания или редактирования товара.";
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('createOrUpdate')
                ->class('btn btn-success')
                ->canSee(!$this->product->exists),

            Button::make('Обновить')
                ->icon('note')
                ->method('createOrUpdate')
                ->class('btn btn-success')
                ->canSee($this->product->exists),

            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->confirm('Вы уверены, что хотите удалить этот товар? Все связанные изображения также будут удалены.')
                ->class('btn btn-danger')
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
            Layout::tabs([
                'Основная информация' => Layout::rows([
                    Group::make([
                        Input::make('product.name')
                            ->title('Название товара')
                            ->placeholder('Введите название товара')
                            ->required()
                            ->help('Полное название товара, которое будет отображаться на сайте'),
                            
                        Input::make('product.slug')
                            ->title('URL-адрес (slug)')
                            ->placeholder('generiruetsya-avtomaticheski')
                            ->required()
                            ->help('Уникальная часть URL для этого товара'),
                    ]),
                    
                    Group::make([
                        Input::make('product.article')
                            ->title('Артикул')
                            ->required()
                            ->help('Уникальный артикул товара'),
                            
                        Input::make('product.code')
                            ->title('Код товара')
                            ->required()
                            ->help('Внутренний код товара'),
                    ]),
                    
                    Quill::make('product.description')
                        ->title('Описание товара')
                        ->toolbar(["text", "color", "header", "list", "format"])
                        ->height('200px')
                        ->help('Подробное описание товара для страницы продукта'),
                        
                    Group::make([
                        Input::make('product.price')
                            ->title('Цена')
                            ->type('number')
                            ->step('0.01')
                            ->required()
                            ->help('Основная цена товара в рублях'),
                            
                        Input::make('product.rating')
                            ->title('Рейтинг')
                            ->type('number')
                            ->step('0.1')
                            ->min(0)
                            ->max(5)
                            ->help('Рейтинг товара от 0 до 5'),
                    ]),
                    
                    Group::make([
                        Input::make('product.brand')
                            ->title('Бренд')
                            ->required()
                            ->help('Производитель товара'),
                            
                        Input::make('product.warranty')
                            ->title('Гарантия')
                            ->help('Срок гарантии (например, "12 месяцев")'),
                    ]),
                    
                    $isCreatingInCategory
                        ? Input::make('product.subcategory_id')
                            ->title('категория')
                            ->value($this->getDefaultSubcategoryId())
                            ->readonly()
                            ->help('Этот товар будет добавлен в: ' . $this->getSubcategoryName())
                        : Select::make('product.subcategory_id')
                            ->title('категория')
                            ->fromModel(Category::class, 'name')
                            ->empty('Не выбрана')
                            ->help('Выберите категорию для этого товара'),
                ]),
                
                'Изображения' => Layout::rows([
                    Upload::make('product.images')
                        ->title('Изображения товара')
                        ->multiple()
                        ->maxFiles(10)
                        ->acceptedFiles('image/*')
                        ->groups('products')
                        ->storage('public')
                        ->help('Загрузите изображения товара (максимум 10)')
                        ->parallelUploads(3),
                ]),
                
                'Характеристики' => Layout::rows([
                    Matrix::make('product.specifications')
                        ->title('Основные характеристики')
                        ->columns([
                            'Параметр' => 'Key',
                            'Значение' => 'Value',
                        ])
                        ->fields([
                            'Key' => Input::make()->placeholder('Например: Вес'),
                            'Value' => Input::make()->placeholder('Например: 1.5 кг'),
                        ])
                        ->value($this->product->specifications ?? [])
                        ->help('Основные параметры товара, которые будут отображаться в карточке'),
                        
                    Matrix::make('product.specificationsB')
                        ->title('Дополнительные характеристики')
                        ->columns([
                            'Название' => 'Name',
                            'Значение' => 'Value',
                        ])
                        ->fields([
                            'Name' => Input::make()->placeholder('Например: Материал'),
                            'Value' => Input::make()->placeholder('Например: Пластик'),
                        ])
                        ->value($this->product->specificationsB ?? [])
                        ->help('Дополнительные параметры товара'),
                ]),
                
                'Преимущества' => Layout::rows([
                    Matrix::make('product.advantages')
                        ->title('Преимущества товара')
                        ->columns([
                            'Заголовок' => 'Title',
                            'Описание' => 'Description',
                        ])
                        ->fields([
                            'Title' => Input::make()->placeholder('Например: Удобное использование'),
                            'Description' => TextArea::make()->placeholder('Подробное описание преимущества')->rows(2),
                        ])
                        ->value($this->product->advantages ?? [])
                        ->help('Перечислите преимущества этого товара перед конкурентами'),
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
                return $categoryId;
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
        return 'Не выбрана подкатегория';
    }

    /**
     * @param Product $product
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Product $product, Request $request)
    {
        $request->validate([
            'product.name' => 'required|string|max:255',
            'product.slug' => 'required|string|max:255|unique:products,slug,'.$product->id,
            'product.article' => 'required|string|max:100',
            'product.price' => 'required|numeric|min:0',
            'product.code' => 'required|string|max:100',
            'product.brand' => 'required|string|max:100',
            'product.subcategory_id' => 'required|exists:categories,id',
            'product.images.*' => 'image|mimes:jpeg,png,jpg,webp',
        ]);

        $data = $request->get('product');

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
        
        $product->fill($data)->save();
        
        if ($request->has('product.images')) {
            if ($product->attachments->isNotEmpty()) {
                $product->attachments()->each(function ($attachment) {
                    Storage::disk('public')->delete($attachment->path);
                    $attachment->delete();
                });
            }
            
            foreach ($request->input('product.images', []) as $imageId) {
                $attachment = Attachment::find($imageId);
                
                if ($attachment) {
                    $attachment->update([
                        'group' => 'products',
                        'product_id' => $product->id,
                    ]);
                    
                    Image::create([
                        'product_id' => $product->id,
                        'url' => $attachment->url,
                        'source' => 'admin',
                        'position' => 0,
                    ]);
                }
            }
        }
        
        Alert::success($this->product->exists ? 'Товар успешно обновлен' : 'Товар успешно создан');
        
        if ($request->has('category_id')) {
            return redirect()->route('platform.category.products', $request->input('category_id'));
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
        if ($product->attachments->isNotEmpty()) {
            $product->attachments()->each(function ($attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            });
        }
        
        $product->images()->delete();
        
        $product->delete();

        Alert::info('Товар успешно удален');
        return redirect()->route('platform.product.list');
    }
}