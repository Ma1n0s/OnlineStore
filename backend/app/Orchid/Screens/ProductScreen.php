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
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Matrix;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        $categoryId = request()->input('category_id');

        $product->load([
            'category', 
            'attachment' => function($query) {
                $query->where('group', 'products')
                    ->orderBy('position');
            },
            'specifications',
            'specificationsB',
            'advantages'
        ]);
        
        return [
            'product' => $product,
            'specifications' => $product->specifications->map(function($item) {
                return ['Key' => $item->key, 'Value' => $item->value];
            })->toArray(),
            'specificationsB' => $product->specificationsB->map(function($item) {
                return ['Name' => $item->name, 'Value' => $item->value];
            })->toArray(),
            'advantages' => $product->advantages->map(function($item) {
                return ['Title' => $item->title, 'Description' => $item->description];
            })->toArray(),
            'category_id' => $categoryId,
            'categories' => $categoryId 
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
     * Determine the back route based on context
     */
    protected function getBackRoute(): string
    {
        $categoryId = request()->input('category_id');
        
        if ($categoryId) {
            return 'platform.category.products';
        }
        
        return 'platform.product.list';
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
                            // ->disabled($this->product->exists),
                            
                        Input::make('product.slug')
                            ->title('URL-адрес (slug)')
                            ->placeholder('generiruetsya-avtomaticheski')
                            ->required()
                            ->help('Уникальная часть URL для этого товара'),
                    ]),
                    
                    Group::make([
                        Input::make('product.article')
                            ->title('Артикул')
                            ->help('Уникальный артикул товара'),
                            // ->disabled($this->product->exists),
                            
                        Input::make('product.code')
                            ->title('Код товара')
                            ->help('Внутренний код товара'),
                            // ->disabled($this->product->exists),
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
                            // ->disabled($this->product->exists),
                            
                        // Input::make('product.rating')
                        //     ->title('Рейтинг')
                        //     ->type('number')
                        //     ->step('0.1')
                        //     ->min(0)
                        //     ->max(5)
                        //     ->help('Рейтинг товара от 0 до 5'),

                        Input::make('product.quantity')
                            ->title('Количество на складе')
                            ->type('number')
                            ->min(0)
                            ->help('Доступное количество товара на складе'),
                    ]),
                    
                    Group::make([
                        Input::make('product.brand')
                            ->title('Бренд')
                            ->help('Производитель товара'),
                            
                        Input::make('product.warranty')
                            ->title('Гарантия')
                            ->help('Срок гарантии (например, "12 месяцев")'),

                        // Input::make('product.bonuses')
                        //     ->title('Бонусы (5% от цены)')
                        //     ->help('Начисляемые бонусы за покупку (5% от цены)')
                        //     ->readonly()
                        //     ->value(function () {
                        //         $price = $this->product->price ?? 0;
                        //         return round($price * 0.03, 2);
                        //     }),

                        Input::make('product.weight')
                            ->title('Вес')
                            ->help('Вес продукта'),
                    ]),

                    Group::make([
                        Select::make('product.type')
                            ->title('Тип товара')
                            ->options([
                                'preorder' => 'Под заказ',
                                'rent' => 'В аренду',
                                'instock' => 'В наличии',
                            ])
                            ->required()
                            ->id('product-type-select')
                            ->help('Выберите тип товара'),

                        Input::make('product.delivery_days')
                            ->title('Время доставки (дни)')
                            ->type('number')
                            ->min(0)
                            ->id('delivery-time-field')
                            ->help('Укажите количество дней для доставки (только для товаров под заказ)'),
                    ]),
                    
                    $isCreatingInCategory
                        ? Input::make('product.category_id')
                            ->title('Категория')
                            ->value($this->getDefaultCategoryId())
                            ->readonly()
                            ->help('Этот товар будет добавлен в: ' . $this->getCategoryName())
                        : Select::make('product.category_id')
                            ->title('Категория')
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
                        ->value(function () {
                            if (!$this->product->exists) {
                                return [];
                            }
                            
                            try {
                                return $this->product->attachment()
                                    ->select('attachments.id', 'attachments.name', 'attachments.original_name', 
                                             'attachments.mime', 'attachments.extension', 'attachments.path', 
                                             'attachments.disk', 'attachments.group', 'attachments.position')
                                    ->where('group', 'products')
                                    ->orderBy('attachments.position')
                                    ->get();
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('Error fetching product attachments', [
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString()
                                ]);
                                return [];
                            }
                        })
                        ->storage('public')
                        ->path('products/' . date('Y/m/d'))
                        ->help('Загрузите изображения товара (максимум 10)')
                        ->parallelUploads(3)
                        ->loadingAsync(),
                ]),
                
                'Характеристики' => Layout::rows([
                    Matrix::make('specifications')
                        ->title('Основные характеристики')
                        ->columns([
                            'Параметр' => 'Key',
                            'Значение' => 'Value',
                        ])
                        ->fields([
                            'Key' => Input::make()->placeholder('Например: Вес'),
                            'Value' => Input::make()->placeholder('Например: 1.5 кг'),
                        ])
                        ->value($this->specifications ?? [])
                        ->help('Основные параметры товара, которые будут отображаться в карточке'),
                        
                    Matrix::make('specificationsB')
                        ->title('Дополнительные характеристики')
                        ->columns([
                            'Название' => 'Name',
                            'Значение' => 'Value',
                        ])
                        ->fields([
                            'Name' => Input::make()->placeholder('Например: Материал'),
                            'Value' => Input::make()->placeholder('Например: Пластик'),
                        ])
                        ->value($this->specificationsB ?? [])
                        ->help('Дополнительные параметры товара'),
                ]),
                
                // 'Преимущества' => Layout::rows([
                //     Matrix::make('advantages')
                //         ->title('Преимущества товара')
                //         ->columns([
                //             'Заголовок' => 'Title',
                //             'Описание' => 'Description',
                //         ])
                //         ->fields([
                //             'Title' => Input::make()->placeholder('Например: Удобное использование'),
                //             'Description' => TextArea::make()->placeholder('Подробное описание преимущества')->rows(2),
                //         ])
                //         ->value($this->advantages ?? [])
                //         ->help('Перечислите преимущества этого товара перед конкурентами'),
                // ]),
            ]),
            Layout::view('admin.product.typeScript')
        ];
    }

    /**
     * Get default subcategory ID when creating product in a category
     */
    protected function getDefaultCategoryId()
    {
        $categoryId = request()->input('category_id');
        return $categoryId ?: null;
    }

    /**
     * Get subcategory name for help text
     */
    protected function getCategoryName()
    {
        $categoryId = $this->getDefaultCategoryId();
        if ($categoryId) {
            return Category::find($categoryId)->name;
        }
        return 'Не выбрана категория';
    }
    /**
     * @param Product $product
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Product $product, Request $request)
    {
        try {


            $validationRules = [
                'product.name' => 'required|string|max:255',
                'product.slug' => 'required|string|max:255|unique:products,slug,'.$product->id,
                'product.article' => 'nullable|string|max:100',
                'product.price' => 'required|numeric|min:0',
                'product.code' => 'nullable|string|max:100',
                'product.brand' => 'nullable|string|max:100',
                'product.category_id' => 'required|exists:categories,id',
                'product.quantity' => 'nullable|integer|min:0',
                'product.type' => 'required|in:preorder,rent,instock',
                'product.weight' => 'nullable|numeric|min:0',
            ];

            // Добавляем правило для delivery_days только если тип "под заказ"
            if ($request->input('product.type') === 'preorder') {
                $validationRules['product.delivery_days'] = 'required|integer|min:0';
            }

            $request->validate($validationRules);

            $data = $request->get('product');

            // Если тип не "под заказ", обнуляем delivery_days
            if ($data['type'] !== 'preorder') {
                $data['delivery_days'] = 0;
            }

            if (!$product->exists && $request->has('category_id')) {
                $categoryId = $request->input('category_id');
                $data['category_id'] = $categoryId;  
            }

            

            $data['rating'] = $data['rating'] ?? 0;
            $data['price'] = (float)$data['price'];
            $data['quantity'] = (int)$data['quantity'];
            $data['bonuses'] = round($data['price'] * 0.05, 2);
            
            if (isset($data['images'])) {
                unset($data['images']);
            }
            
            // Сохраняем продукт
            $product->fill($data)->save();
            
            // Обработка характеристик
            $this->processSpecifications($product, $request);
            $this->processSpecificationsB($product, $request);
            $this->processAdvantages($product, $request);
            
            // Handle product images
            if ($request->has('product.images')) {
                $newImageIds = $request->input('product.images', []);
                
                // Convert to array if needed
                if (!is_array($newImageIds)) {
                    $newImageIds = [];
                }
                
                \Illuminate\Support\Facades\Log::info('Processing product images', [
                    'product_id' => $product->id,
                    'new_image_ids' => $newImageIds,
                    'count' => count($newImageIds)
                ]);
                
                // Get current attachments for this product
                $currentAttachments = DB::table('attachmentable')
                    ->where('attachmentable_id', $product->id)
                    ->where('attachmentable_type', get_class($product))
                    ->join('attachments', 'attachments.id', '=', 'attachmentable.attachment_id')
                    ->where('attachments.group', 'products')
                    ->select('attachments.id')
                    ->get()
                    ->pluck('id')
                    ->toArray();
                
                \Illuminate\Support\Facades\Log::info('Current vs New attachments', [
                    'current_ids' => $currentAttachments,
                    'new_ids' => $newImageIds
                ]);
                
                // Find attachments to remove (in current but not in new list)
                $toRemove = array_diff($currentAttachments, $newImageIds);
                
                // Remove outdated attachments
                foreach ($toRemove as $attachmentId) {
                    // Remove link between product and attachment
                    DB::table('attachmentable')
                        ->where('attachment_id', $attachmentId)
                        ->where('attachmentable_id', $product->id)
                        ->where('attachmentable_type', get_class($product))
                        ->delete();
                    
                    \Illuminate\Support\Facades\Log::info('Removed attachment link', [
                        'attachment_id' => $attachmentId,
                        'product_id' => $product->id
                    ]);
                    
                    // Check if attachment is used elsewhere before deleting
                    $usageCount = DB::table('attachmentable')
                        ->where('attachment_id', $attachmentId)
                        ->count();
                    
                    if ($usageCount === 0) {
                        // Actually delete the attachment if not used elsewhere
                        $attachment = Attachment::find($attachmentId);
                        if ($attachment) {
                            $attachment->delete();
                            \Illuminate\Support\Facades\Log::info('Deleted unused attachment', [
                                'attachment_id' => $attachmentId
                            ]);
                        }
                    }
                }
                
                // The file IDs come in the exact order they are in the form
                // We directly use this order for the positions
                $firstImageUrl = null;
                
                foreach ($newImageIds as $index => $attachmentId) {
                    // Set position directly based on the order in the form
                    DB::table('attachments')
                        ->where('id', $attachmentId)
                        ->update([
                            'position' => $index, // Position is based on exact array order
                            'group' => 'products'
                        ]);
                    
                    // For the first image, get URL for main_image
                    if ($index === 0 && !$firstImageUrl) {
                        $attachment = Attachment::find($attachmentId);
                        if ($attachment) {
                            $firstImageUrl = $attachment->url;
                        }
                    }
                    
                    // Link attachment to product
                    DB::table('attachmentable')->updateOrInsert(
                        [
                            'attachment_id' => $attachmentId,
                            'attachmentable_id' => $product->id,
                            'attachmentable_type' => get_class($product)
                        ],
                        []
                    );
                }
                
                // Update product's main_image
                if (Schema::hasColumn('products', 'main_image')) {
                    if (!empty($newImageIds) && $firstImageUrl) {
                        $product->main_image = $firstImageUrl;
                        $product->save();
                        
                        \Illuminate\Support\Facades\Log::info('Updated main image', [
                            'product_id' => $product->id,
                            'main_image' => $firstImageUrl
                        ]);
                    } else {
                        $product->main_image = null;
                        $product->save();
                        
                        \Illuminate\Support\Facades\Log::info('Cleared main image', [
                            'product_id' => $product->id
                        ]);
                    }
                }
            }

            Alert::success($this->product->exists ? 'Товар успешно обновлен' : 'Товар успешно создан');
            
            if ($request->has('category_id')) {
                return redirect()->route('platform.category.products', $request->input('category_id'));
            }
            
            return redirect()->route('platform.product.list');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating/updating product', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            Alert::error('Ошибка при сохранении товара: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    protected function processSpecifications(Product $product, Request $request)
    {
        $specifications = $request->input('specifications', []);
        
        $product->specifications()->delete();
        
        foreach ($specifications as $index => $spec) {
            if (!empty($spec['Key']) && !empty($spec['Value'])) {
                $product->specifications()->create([
                    'key' => $spec['Key'],
                    'value' => $spec['Value'],
                    'position' => $index
                ]);
            }
        }
    }

    protected function processSpecificationsB(Product $product, Request $request)
    {
        $specificationsB = $request->input('specificationsB', []);
        
        $product->specificationsB()->delete();
        
        foreach ($specificationsB as $index => $spec) {
            if (!empty($spec['Name']) && !empty($spec['Value'])) {
                $product->specificationsB()->create([
                    'name' => $spec['Name'],
                    'value' => $spec['Value'],
                    'position' => $index
                ]);
            }
        }
    }

    protected function processAdvantages(Product $product, Request $request)
    {
        $advantages = $request->input('advantages', []);
        
        $product->advantages()->delete();
        
        foreach ($advantages as $index => $advantage) {
            if (!empty($advantage['Title']) && !empty($advantage['Description'])) {
                $product->advantages()->create([
                    'title' => $advantage['Title'],
                    'description' => $advantage['Description'],
                    'position' => $index
                ]);
            }
        }
    }


    /**
     * @param Product $product
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Product $product)
    {
        try {
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
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error removing product', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            Alert::error('Ошибка при удалении товара: ' . $e->getMessage());
            return back();
        }
    }
}