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
                    ->orderBy('sort');
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
                                return $product->attachment()
                                    ->select('attachments.*') // Explicitly select from attachments
                                    ->where('group', 'products')
                                    ->orderBy('sort')
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
            $request->validate([
                'product.name' => 'required|string|max:255',
                'product.slug' => 'required|string|max:255|unique:products,slug,'.$product->id,
                'product.article' => 'required|string|max:100',
                'product.price' => 'required|numeric|min:0',
                'product.code' => 'required|string|max:100',
                'product.brand' => 'required|string|max:100',
                'product.category_id' => 'required|exists:categories,id',
            ]);

            $data = $request->get('product');

            if (!$product->exists && $request->has('category_id')) {
                $categoryId = $request->input('category_id');
                $data['category_id'] = $categoryId;  
            }

            $data['rating'] = $data['rating'] ?? 0;
            $data['price'] = (float)$data['price'];
            
            if (isset($data['images'])) {
                unset($data['images']);
            }
            
            // Сохраняем продукт
            $product->fill($data)->save();
            
            // Обработка характеристик
            $this->processSpecifications($product, $request);
            $this->processSpecificationsB($product, $request);
            $this->processAdvantages($product, $request);
            
            if ($request->has('product.images')) {
                $imageIds = $request->input('product.images', []);
                
                \Illuminate\Support\Facades\Log::info('Product Images being processed', [
                    'product_id' => $product->id,
                    'image_ids' => $imageIds,
                    'is_array' => is_array($imageIds),
                    'count' => is_array($imageIds) ? count($imageIds) : 0,
                    'request_data' => $request->all(),
                ]);
                
                $publicPath = storage_path('app/public');
                $datePath = storage_path('app/public/' . date('Y/m/d'));
                
                \Illuminate\Support\Facades\Log::info('Storage directories', [
                    'public_exists' => file_exists($publicPath),
                    'public_writable' => is_writable($publicPath),
                    'date_path' => $datePath,
                    'date_exists' => file_exists($datePath),
                    'date_writable' => file_exists($datePath) ? is_writable($datePath) : false,
                ]);
                
                if ($product->attachments->isNotEmpty()) {
                    $currentAttachmentIds = $product->attachment()->where('group', 'products')->pluck('id')->toArray();
                    
                    \Illuminate\Support\Facades\Log::info('Current attachment IDs', [
                        'current_ids' => $currentAttachmentIds,
                        'new_ids' => $imageIds,
                    ]);
                    
                    $attachmentsToDelete = array_diff($currentAttachmentIds, $imageIds);
                    
                    \Illuminate\Support\Facades\Log::info('Attachments to delete', [
                        'to_delete' => $attachmentsToDelete
                    ]);
                    
                    if (!empty($attachmentsToDelete)) {
                        foreach ($attachmentsToDelete as $attachmentId) {
                            $attachment = Attachment::find($attachmentId);
                            if ($attachment) {
                                Storage::disk('public')->delete($attachment->path);
                                $attachment->delete();
                                \Illuminate\Support\Facades\Log::info('Deleted attachment', [
                                    'attachment_id' => $attachmentId
                                ]);
                            }
                        }
                    }
                    
                    if (!empty($attachmentsToDelete)) {
                        \App\Models\Image::whereIn('attachment_id', $attachmentsToDelete)->delete();
                    }
                }
                
                if (is_array($imageIds) && count($imageIds) > 0) {
                    $maxPosition = \App\Models\Image::where('product_id', $product->id)
                        ->where('source', 'admin')
                        ->max('position') ?? -1;
                    
                    foreach ($imageIds as $index => $imageId) {
                        $attachment = Attachment::find($imageId);
                        
                        if ($attachment) {
                            \Illuminate\Support\Facades\Log::info('Processing Attachment', [
                                'id' => $attachment->id,
                                'name' => $attachment->name,
                                'url' => $attachment->url,
                                'group' => $attachment->group,
                                'disk' => $attachment->disk,
                                'path' => $attachment->path,
                            ]);
                            
                            $attachment->forceFill([
                                'group' => 'products',
                            ])->save();
                            
                            DB::table('attachmentable')->updateOrInsert(
                                [
                                    'attachment_id' => $attachment->id,
                                    'attachmentable_id' => $product->id,
                                    'attachmentable_type' => get_class($product),
                                ],
                                []
                            );
                            
                            \Illuminate\Support\Facades\Log::info('Linked attachment to product', [
                                'attachment_id' => $attachment->id,
                                'product_id' => $product->id,
                            ]);
                            
                            $imageExists = \App\Models\Image::where('product_id', $product->id)
                                ->where('attachment_id', $attachment->id)
                                ->exists();
                            
                            \Illuminate\Support\Facades\Log::info('Image record check', [
                                'exists' => $imageExists,
                                'attachment_id' => $attachment->id,
                                'product_id' => $product->id,
                            ]);
                            
                            if (!$imageExists) {
                                $position = $maxPosition + $index + 1;
                                
                                \Illuminate\Support\Facades\Log::info('Creating image', [
                                    'product_id' => $product->id,
                                    'url' => $attachment->url,
                                    'source' => 'admin',
                                    'position' => $position,
                                    'attachment_id' => $attachment->id,
                                ]);
                                
                                try {
                                    $image = Image::create([
                                        'product_id' => $product->id,
                                        'url' => $attachment->url,
                                        'source' => 'admin',
                                        'position' => $position,
                                        'attachment_id' => $attachment->id,
                                    ]);
                                    
                                    \Illuminate\Support\Facades\Log::info('Image created successfully', [
                                        'image_id' => $image->id,
                                    ]);
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::error('Error creating image record', [
                                        'error' => $e->getMessage(),
                                        'trace' => $e->getTraceAsString(),
                                    ]);
                                }
                            }
                        } else {
                            \Illuminate\Support\Facades\Log::warning('Attachment not found', [
                                'image_id' => $imageId
                            ]);
                        }
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
        $specifications = $request->input('product.specifications', []);
        
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
        $specificationsB = $request->input('product.specificationsB', []);
        
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
        $advantages = $request->input('product.advantages', []);
        
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