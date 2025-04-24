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
        $product->load('subcategory');
        $categoryId = request()->input('category_id');
        
        // Debug - log product and its attachments
        \Illuminate\Support\Facades\Log::info('Product data for Orchid screen', [
            'product_id' => $product->id,
            'exists' => $product->exists,
            'attachments_count' => $product->exists ? $product->attachment()->count() : 0,
            'attachments' => $product->exists ? $product->attachment()->get()->pluck('id')->toArray() : []
        ]);
        
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
                            ->help('Выберите подкатегорию для этого товара'),
                ]),
                
                'Изображения' => Layout::rows([
                    Upload::make('product.images')
                        ->title('Изображения товара')
                        ->multiple()
                        ->maxFiles(10)
                        ->acceptedFiles('image/*')
                        ->groups('products')
                        ->value(function () {
                            $product = $this->product;
                            if (!$product->exists) {
                                return [];
                            }
                            
                            try {
                                // Get attachments from the attachmentable table directly
                                $attachmentIds = DB::table('attachmentable')
                                    ->where('attachmentable_id', $product->id)
                                    ->where('attachmentable_type', get_class($product))
                                    ->pluck('attachment_id')
                                    ->toArray();
                                    
                                \Illuminate\Support\Facades\Log::info('Fetching attachments from attachmentable table', [
                                    'product_id' => $product->id,
                                    'attachment_ids' => $attachmentIds
                                ]);
                                
                                if (empty($attachmentIds)) {
                                    return [];
                                }
                                
                                return Attachment::whereIn('id', $attachmentIds)
                                    ->where('group', 'products')
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
protected function getDefaultSubcategoryId()
{
    $categoryId = request()->input('category_id');
    
    if ($categoryId) {
        $currentCategory = Category::find($categoryId);
        
        if ($currentCategory && $currentCategory->parent_id) {
            return $categoryId;
        }
        
        $subcategories = Category::where('parent_id', $categoryId)->get();
        
        if ($subcategories->isNotEmpty()) {
            return $subcategories->first()->id;
        }
        return $categoryId;
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
        $category = Category::find($subcategoryId);
        return $category ? $category->name : 'Неизвестная категория';
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
                'product.subcategory_id' => 'required|exists:categories,id',
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
            $data['price'] = (float)$data['price'];
            
            // Ensure these are arrays before saving
            $data['specifications'] = isset($data['specifications']) ? (array)$data['specifications'] : [];
            $data['advantages'] = isset($data['advantages']) ? (array)$data['advantages'] : [];
            $data['specificationsB'] = isset($data['specificationsB']) ? (array)$data['specificationsB'] : [];
            
            // Convert arrays to JSON strings manually if needed
            if (isset($data['specifications']) && !is_string($data['specifications'])) {
                $data['specifications'] = json_encode($data['specifications']);
            }
            
            if (isset($data['advantages']) && !is_string($data['advantages'])) {
                $data['advantages'] = json_encode($data['advantages']);
            }
            
            if (isset($data['specificationsB']) && !is_string($data['specificationsB'])) {
                $data['specificationsB'] = json_encode($data['specificationsB']);
            }
            
            // Remove images field from the data to prevent 'Array to string conversion' error
            if (isset($data['images'])) {
                unset($data['images']);
            }
            
            $product->fill($data)->save();
            
            if ($request->has('product.images')) {
                // Get the new image IDs
                $imageIds = $request->input('product.images', []);
                
                // Log for debugging
                \Illuminate\Support\Facades\Log::info('Product Images being processed', [
                    'product_id' => $product->id,
                    'image_ids' => $imageIds,
                    'is_array' => is_array($imageIds),
                    'count' => is_array($imageIds) ? count($imageIds) : 0,
                    'request_data' => $request->all(),
                ]);
                
                // Check available upload directories
                $publicPath = storage_path('app/public');
                $datePath = storage_path('app/public/' . date('Y/m/d'));
                
                \Illuminate\Support\Facades\Log::info('Storage directories', [
                    'public_exists' => file_exists($publicPath),
                    'public_writable' => is_writable($publicPath),
                    'date_path' => $datePath,
                    'date_exists' => file_exists($datePath),
                    'date_writable' => file_exists($datePath) ? is_writable($datePath) : false,
                ]);
                
                // Delete old attachments if we have new ones or if the array is empty (which means user removed all images)
                if ($product->attachments->isNotEmpty()) {
                    // Get the current attachment IDs
                    $currentAttachmentIds = $product->attachment()
                        ->select('attachments.id') // Явно указываем, что мы хотим id из таблицы attachments
                        ->where('group', 'products')
                        ->pluck('id')
                        ->toArray();
                    
                    \Illuminate\Support\Facades\Log::info('Current attachment IDs', [
                        'current_ids' => $currentAttachmentIds,
                        'new_ids' => $imageIds,
                    ]);
                    
                    // Find which attachments to delete
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
                    
                    // Also delete corresponding image records
                    if (!empty($attachmentsToDelete)) {
                        \App\Models\Image::whereIn('attachment_id', $attachmentsToDelete)->delete();
                    }
                }
                
                // Check if we have image IDs
                if (is_array($imageIds) && count($imageIds) > 0) {
                    // Get current max position for this product's images
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
                            
                            // Make sure we're setting up the attachment properly
                            $attachment->forceFill([
                                'group' => 'products',
                            ])->save();
                            
                            // Explicitly link the attachment to the product
                            // This uses the 'attachmentable' junction table
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
                            
                            // Check if an image record already exists for this attachment
                            $imageExists = \App\Models\Image::where('product_id', $product->id)
                                ->where('attachment_id', $attachment->id)
                                ->exists();
                            
                            \Illuminate\Support\Facades\Log::info('Image record check', [
                                'exists' => $imageExists,
                                'attachment_id' => $attachment->id,
                                'product_id' => $product->id,
                            ]);
                            
                            if (!$imageExists) {
                                // Calculate a unique position for each image
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