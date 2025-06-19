<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductImportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected ProductImportService $productImportService;

    public function __construct(ProductImportService $productImportService)
    {
        $this->productImportService = $productImportService;
    }

    /**
     * Transform and sort product images for API responses
     * 
     * @param Product $product
     * @return array
     */






     public function processProduct(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product' => 'required|array',
            'product.name' => 'required|string|max:255',
            'product.quantity' => 'required|integer|min:0',
            'product.price' => 'required|numeric|min:0',
            'product.type' => 'required|string|max:100',
            'product.delivery_days' => 'nullable|integer|min:0',
            'product.article' => 'required|string',
            'product.site_article' => 'required|string',
            'product.id_category' => 'required|integer', // pro_id категории
            'categories' => 'required|array',
            'categories.*.pro_id' => 'required|integer',
            'categories.*.name' => 'required|string',
            'categories.*.id_parent' => 'nullable|integer'
        ]);

        $token = $request->header('Authorization');

        if($token !== env('services.external_api.token', '')) {
            return response()->json(['error'=>'auth error'], 401);
        }
    
        return DB::transaction(function () use ($validated) {
            // 1. Обрабатываем категории
            $categoryMap = []; // pro_id => local_id
            
            // Сначала корневые категории (без родителей)
            foreach ($validated['categories'] as $categoryData) {
                if (empty($categoryData['id_parent'])) {
                    $category = Category::updateOrCreate(
                        ['pro_id' => $categoryData['pro_id']],
                        ['name' => $categoryData['name'], 'slug' => Str::slug($categoryData['name'].'-'.$categoryData['pro_id'])]
                    );
                    $categoryMap[$categoryData['pro_id']] = $category->id;
                }
            }
            
            // Затем дочерние категории
            foreach ($validated['categories'] as $categoryData) {
                if (!empty($categoryData['id_parent'])) {
                    $parentId = $categoryMap[$categoryData['id_parent']] ?? null;
                    if ($parentId) {
                        $category = Category::updateOrCreate(
                            ['pro_id' => $categoryData['pro_id']],
                            [
                                'name' => $categoryData['name'],
                                'parent_id' => $parentId,
                                'slug' => Str::slug($categoryData['name'])
                            ]
                        );
                        $categoryMap[$categoryData['pro_id']] = $category->id;
                    }
                }
            }
            
            // 2. Получаем local_id категории продукта
            $productCategoryId = $categoryMap[$validated['product']['id_category']] ?? null;
            if (!$productCategoryId) {
                throw new \Exception("Категория продукта не найдена");
            }
    
            // 3. Создаем/обновляем продукт
            $productData = $validated['product'];
            $slug = Str::slug($productData['name']);
            
            $existingProduct = Product::where('slug', $slug)
                ->where('article', '!=', $productData['article'])
                ->first();
            
            if ($existingProduct) {
                $slug = $slug . '-' . substr(uniqid(), -4);
            }

            $product = Product::updateOrCreate(
                ['article' => $productData['article']],
                [
                    'name' => $productData['name'],
                    'quantity' => $productData['quantity'],
                    'price' => $productData['price'],
                    'type' => $productData['type'],
                    'delivery_days' => $productData['delivery_days'] ?? 0,
                    'article' => $productData['article'],
                    'code' => $productData['site_article'],
                    'slug' => $slug,
                    'category_id' => $productCategoryId
                ]
            );
    
            return response()->json([
                'message' => $product->wasRecentlyCreated ? 'Продукт создан' : 'Продукт обновлен',
                'product' => $product,
                'category_id' => $productCategoryId,
                'status' => $product->wasRecentlyCreated ? 'created' : 'updated'
            ], 201);
        });
    }

    public function checkSlug(Request $request): JsonResponse
    {
        $request->validate([
            'slug' => 'required|string',
            'product_id' => 'nullable|integer|exists:products,id'
        ]);

        $slug = $request->input('slug');
        $productId = $request->input('product_id');

        $exists = Product::where('slug', $slug)
            ->when($productId, function($query) use ($productId) {
                $query->where('id', '!=', $productId);
            })
            ->exists();

        return response()->json([
            'available' => !$exists,
            'slug' => $slug
        ]);
    }
    
    private function updateOrCreateCategory(array $data, ?int $parentId = null): Category
    {
        return Category::updateOrCreate(
            ['pro_id' => $data['pro_id']],
            [
                'name' => $data['name'],
                'parent_id' => $parentId,
                'slug' => Str::slug($data['name'])
            ]
        );
    }

    protected function processCategories(array $categories, int $mainCategoryId): int
    {
        $categoryMap = [];
        
        // Сначала обрабатываем категории без родителей
        foreach ($categories as $categoryData) {
            if (empty($categoryData['id_parent'])) {
                $category = $this->updateOrCreateCategory($categoryData);
                $categoryMap[$categoryData['pro_id']] = $category->id;
            }
        }
        
        // Затем обрабатываем дочерние категории
        foreach ($categories as $categoryData) {
            if (!empty($categoryData['id_parent'])) {
                if (!isset($categoryMap[$categoryData['id_parent']])) {
                    continue; // Или можно выбросить исключение
                }
                
                $categoryData['parent_id'] = $categoryMap[$categoryData['id_parent']];
                $category = $this->updateOrCreateCategory($categoryData);
                $categoryMap[$categoryData['pro_id']] = $category->id;
            }
        }
        
        return $categoryMap[$mainCategoryId] ?? $mainCategoryId;
    }




















    private function transformProductImages(Product $product): array
    {
        $allImages = $product->all_images;
        
        // Ensure images are sorted by position
        if ($allImages instanceof \Illuminate\Support\Collection) {
            $allImages = $allImages->sortBy('position');
        }
        
        return [
            'images' => $allImages,
            'main_image' => $product->main_image
        ];
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query)) {
            return response()->json([
                'products' => [],
                'categories' => []
            ]);
        }

        // Поиск продуктов
        $products = Product::where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get();


        if ($products->isEmpty()) {
            $products = Product::where(function($q) use ($query) {
                    // Вариант 1: LIKE с более широким поиском
                    // $q->where('name', 'like', "%".substr($query, 0, -1)."%")
                    //     ->orWhere('name', 'like', "%".substr($query, 1)."%");
                    
                    // Вариант 2: Фонетический поиск (SOUNDEX)
                    // $q->orWhereRaw("SOUNDEX(name) = SOUNDEX(?)", [$query]);
                    $q->orWhereRaw("similarity(name, ?) > 0.3", [$query]);
                    
                    
                })
                ->limit(5)
                ->get();
        }
        

        // Поиск категорий
        $categories = Category::where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        $categoryController = app('App\Http\Controllers\Api\CategoryController');
        if ($categories) {
            $temp = [];
            foreach ($categories as $child) {
                $transformedChild = $categoryController->transformImagesPaths($child);
                $transformedChild->path = $categoryController->getCategoryPath($child);
                $transformedChild->haveProducts = $categoryController->hasProducts($child);
                $transformedChild->root = $categoryController->getRootParent($child)->name;
                $temp[] = $transformedChild;
            }
            $categories = $temp;
        }

        return response()->json([
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * Получить список всех продуктов
     *
     * @return JsonResponse
     */
    // public function index(): JsonResponse
    // {
    //     $products = Product::with(['category', 'specificationCategories.specifications', 'images'])
    //         ->paginate(10);
        
    //     $categoryController = app('App\Http\Controllers\Api\CategoryController');
            
    //     return response()->json(
    //         $products->map(function($product) use ($categoryController) {
    //             // Transform category image paths if category exists
    //             if ($product->category) {
    //                 $product->category = $categoryController->transformImagesPaths($product->category);
    //             }
                
    //             return [
    //                 'id' => $product->id,
    //                 'name' => $product->name,
    //                 'price' => $product->price,
    //                 'description' => $product->description,
    //                 'category' => $product->category ? [
    //                     'id' => $product->category->id,
    //                     'name' => $product->category->name,
    //                     'slug' => $product->category->slug,
    //                     'image_url' => $product->category->image_url,
    //                     'description_image_url' => $product->category->description_image_url,
    //                 ] : null,
    //                 'specifications' => $product->specificationCategories->mapWithKeys(function ($category) {
    //                     return [$category->name => $category->specifications->mapWithKeys(function ($spec) {
    //                         return [$spec->name => $spec->value];
    //                     })];
    //                 }),
    //                 'images' => $this->transformProductImages($product)['images'],
    //                 'main_image' => $this->transformProductImages($product)['main_image'],
    //             ];
    //         })
    //     );
    // }

    /**
     * Получить продукт по slug
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function getBySlug(string $slug): JsonResponse
{
    $product = Product::with(['category', 'specifications', 'specificationsB', 'advantages', 'images'])
        ->where('slug', $slug)
        ->first();
    
    if (!$product) {
        return response()->json(['message' => 'Продукт не найден'], 404);
    }

    // Добавляем проверку количества
    $count = $product->quantity;
    if($count == 0) {
        $count = 'Нет в наличии';
    } else if($count > 5) {
        $count = 'Много';
    } else {
        $count = 'В наличии';
    }

    // Получаем путь категорий
    $categoryPath = [];
    if ($product->category) {
        $categoryPath = $product->category->getPath()->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug
            ];
        });
    }

    return response()->json([
        'id' => $product->id,
        'name' => $product->name,
        'price' => $product->price,
        'old_price' => $product->old_price,
        'description' => $product->description,
        'short_description' => $product->short_description,
        'count' => $count,
        'rating' => $product->rating,
        'slug' => $product->slug,
        'brand' => $product->brand,
        'article' => $product->article,
        'category' => $categoryPath,
        'specifications' => $product->specifications,
        'specificationsB' => $product->specificationsB,
        'advantages' => $product->advantages,
        'type' => $product->type,
        'delivery_days' => $product->delivery_days,
     // 'specifications' => $product->specificationCategories->mapWithKeys(function ($category) {
     //     return [$category->name => $category->specifications->mapWithKeys(function ($spec) {
     //         return [$spec->name => $spec->value];
     //     })];
     // }),
        'images' => $this->transformProductImages($product)['images'],
        'main_image' => $this->transformProductImages($product)['main_image'],
    ]);
}

    /**
     * Получить детальную информацию о продукте
     *
     * @param string $id
     * @return JsonResponse
     */
    // public function show(string $id): JsonResponse
    // {
    //     $product = Product::with(['specificationCategories.specifications', 'images', 'category'])->findOrFail($id);

    //     // Получаем путь категорий от корня до категории продукта
    //     $categoryPath = [];
    //     if ($product->category) {
    //         $categoryPath = $product->category->getPath()->map(function($category) {
    //             return [
    //                 'id' => $category->id,
    //                 'name' => $category->name,
    //                 'slug' => $category->slug
    //             ];
    //         });
    //     }

    //     $response = [
    //         'id' => $product->id,
    //         'name' => $product->name,
    //         'price' => $product->price,
    //         'old_price' => $product->old_price,
    //         'description' => $product->description,
    //         'short_description' => $product->short_description,
    //         'in_stock' => (bool)$product->in_stock,
    //         'is_featured' => (bool)$product->is_featured,
    //         'sku' => $product->sku,
    //         'barcode' => $product->barcode,
    //         'quantity' => $product->quantity,
    //         'rating' => $product->rating,
    //         'category' => $product->category ? [
    //             'id' => $product->category->id,
    //             'name' => $product->category->name,
    //             'slug' => $product->category->slug,
    //             'image_url' => $product->category->image_url,
    //             'description_image_url' => $product->category->description_image_url,
    //         ] : null,
    //         'category_path' => $categoryPath,
    //         'specifications' => $product->specificationCategories->mapWithKeys(function ($category) {
    //             return [$category->name => $category->specifications->mapWithKeys(function ($spec) {
    //                 return [$spec->name => $spec->value];
    //             })];
    //         }),
    //         'images' => $this->transformProductImages($product)['images'],
    //         'main_image' => $this->transformProductImages($product)['main_image'],
    //     ];
        
    //     return response()->json($response);
    // }

    /**
     * Создать новый продукт из данных скрапера
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Валидация запроса
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'in_stock' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sku' => 'nullable|string',
            'barcode' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'old_price' => 'nullable|numeric',
            'rating' => 'nullable|numeric',
            'category' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'specifications' => 'nullable|array',
        ]);
        
        // Находим или создаем категорию
        $categoryName = $request->input('category', 'Другое');
        
        // Проверяем, содержит ли категория путь через "/"
        $categoryPath = explode('/', $categoryName);
        
        // Находим или создаем корневую категорию и все подкатегории в пути
        $category = null;
        $parentId = null;
        
        foreach ($categoryPath as $index => $name) {
            $name = trim($name);
            if (empty($name)) continue;
            
            $slug = \Illuminate\Support\Str::slug($name);
            
            $category = \App\Models\Category::firstOrCreate(
                [
                    'name' => $name,
                    'parent_id' => $parentId
                ],
                [
                    'slug' => $slug,
                    'description' => "Категория товаров: {$name}"
                ]
            );
            
            $parentId = $category->id;
        }
        
        // Создаем продукт
        $product = new Product();
        $product->name = $validated['name'];
        $product->price = (float)$validated['price'];
        $product->description = $validated['description'] ?? '';
        $product->short_description = $validated['short_description'] ?? '';
        $product->in_stock = $validated['in_stock'] ?? true;
        $product->is_featured = $validated['is_featured'] ?? false;
        $product->sku = $validated['sku'] ?? '';
        $product->barcode = $validated['barcode'] ?? '';
        $product->quantity = (int)($validated['quantity'] ?? 0);
        $product->old_price = (float)($validated['old_price'] ?? 0);
        $product->rating = (float)($validated['rating'] ?? 0);
        $product->category_id = $category ? $category->id : null;
        $product->save();
        
        // Обработка изображений
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                
                $product->images()->create([
                    'url' => '/storage/' . $path,
                    'alt' => $product->name,
                ]);
            }
        }
        
        // Обработка характеристик
        if (isset($validated['specifications']) && is_array($validated['specifications'])) {
            foreach ($validated['specifications'] as $categoryName => $specs) {
                $specCategory = $product->specificationCategories()->create([
                    'name' => $categoryName
                ]);
                
                foreach ($specs as $name => $value) {
                    $specCategory->specifications()->create([
                        'name' => $name,
                        'value' => $value
                    ]);
                }
            }
        }
        
        return response()->json([
            'id' => $product->id,
            'category' => $product->category ? $product->category->name : null,
            'message' => 'Продукт успешно создан'
        ], 201);
    }

    /**
     * Удалить продукт
     *
     * @param Product $product
     * @return JsonResponse
     */
    // public function destroy(Request $request, $article): JsonResponse
    // {

    //     $token = $request->header('Authorization');

    //     if($token !== env('services.external_api.token', '')) {
    //         return response()->json(['error'=>'auth error'], 500);
    //     }

    //     $product = Product::where('article', $article)->firstOrFail();

    //     try {
    //         $product->delete();
            
    //         return response()->json([
    //             'message' => 'Продукт успешно удален',
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Ошибка при удалении продукта',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    /**
     * Получить список продуктов по категории и подкатегории
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getProductsByCategory(Request $request): JsonResponse
    {
        // Валидируем входные данные
        $request->validate([
            'category_id' => 'nullable|integer|exists:categories,id',
            'limit' => 'nullable|integer|min:1|max:50',
            'page' => 'nullable|integer|min:1',
        ]);
        
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        
        $query = Product::with(['category', 'specificationCategories.specifications', 'images']);
        
        // Фильтр по категории
        if ($request->has('category_id')) {
            $categoryId = $request->input('category_id');
            $category = Category::find($categoryId);
            
            if ($category) {
                // Получаем все ID категорий-потомков, включая текущую категорию
                $categoryIds = [$category->id];
                
                // Загружаем всех потомков
                $category->load('descendants');
                
                // Рекурсивно собираем всех потомков
                $this->collectDescendantIds($category, $categoryIds);
                
                // Фильтруем продукты по всем категориям
                $query->whereIn('category_id', $categoryIds);
            }
        }
        
        $products = $query->paginate($limit, ['*'], 'page', $page);
        
        return response()->json(
            $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'old_price' => $product->old_price,
                    'description' => $product->description,
                    'short_description' => $product->short_description,
                    'rating' => $product->rating,
                    // 'category' => $product->category ? [
                    //     'id' => $product->category->id,
                    //     'name' => $product->category->name,
                    //     'slug' => $product->category->slug,
                    //     'image_url' => $product->category->image_url,
                    //     'description_image_url' => $product->category->description_image_url,
                    // ] : null,
                    'images' => $this->transformProductImages($product)['images'],
                ];
            })
        );
    }
    
    /**
     * Рекурсивно собирает ID всех потомков категории
     */
    private function collectDescendantIds($category, &$categoryIds): void
    {
        foreach ($category->children as $child) {
            $categoryIds[] = $child->id;
            if ($child->children->count() > 0) {
                $this->collectDescendantIds($child, $categoryIds);
            }
        }
    }

    /**
     * Получить продукты по ID категории
     *
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
    public function getProductsByCategoryId(Request $request, Category $category): JsonResponse
    {
        // Валидируем входные данные
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:50',
            'sort' => 'nullable|string|in:price_asc,price_desc,newest,oldest,name_asc,name_desc',
        ]);
        
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 12);
        $sort = $request->input('sort', 'newest');

        // Преобразуем ID изображений категории в URL
        $category = app('App\Http\Controllers\Api\CategoryController')->transformImagesPaths($category);
        
        // Получаем все ID категорий-потомков, включая текущую категорию
        $categoryIds = [$category->id];
        
        // Загружаем всех потомков
        $category->load('descendants');
        
        // Рекурсивно собираем всех потомков
        $this->collectDescendantIds($category, $categoryIds);
        
        // Фильтруем продукты по всем категориям
        $query = Product::with(['category', 'images'])
            ->where(function($query) use ($categoryIds) {
                $query->where(function($subQuery) use ($categoryIds) {
                    $subQuery->whereIn('category_id', $categoryIds);
                });
            });
            
        // Применяем сортировку
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
        }
        
        $products = $query->paginate($limit, ['*'], 'page', $page);
        
        // Форматируем данные для каждого продукта
        $formattedProducts = collect($products->items())->map(function($product) {
            $images = $this->transformProductImages($product);
            
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'old_price' => $product->old_price,
                'description' => $product->description,
                'short_description' => $product->short_description,
                'in_stock' => (bool)$product->in_stock,
                'rating' => $product->rating,
                'brand' => $product->brand,
                'article' => $product->article,
                'slug' => $product->slug,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'title' => $product->category->title,
                    'slug' => $product->category->slug,
                    'image_url' => $product->category->image_url,
                    'description_image_url' => $product->category->description_image_url,
                ] : null,
                'images' => $images['images'],
                'main_image' => $images['main_image'],
            ];
        });
        
        return response()->json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'title' => $category->title,
                'slug' => $category->slug,
                'description' => $category->description,
                'image_url' => $category->image_url,
                'description_image_url' => $category->description_image_url,
            ],
            'products' => $formattedProducts,
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more' => $products->hasMorePages(),
            ],
        ]);
    }

    /**
     * Получить продукты по slug категории
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function getProductsByCategorySlug(Request $request, string $slug): JsonResponse
    {
        // Находим категорию по slug
        $category = Category::where('slug', $slug)->first();
        
        
        if (!$category) {
            return response()->json(['message' => 'Категория не найдена'], 404);
        }
        
        // Валидируем входные данные
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:50',
            'sort' => 'nullable|string|in:price_asc,price_desc',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0|gt:price_min',
            'brands' => 'nullable|array',
            'brands.*' => 'string',
            'addition_data' => 'nullable|boolean'
        ]);
        
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'newest');
        $additionData = $request->boolean('addition_data', false);
        
        // Преобразуем ID изображений категории в URL
        $category = app('App\Http\Controllers\Api\CategoryController')->transformImagesPaths($category);

        // Получаем все ID категорий-потомков, включая текущую категорию
        $categoryIds = [$category->id];
        
        // Загружаем всех потомков
        $category->load('descendants');
        
        // Рекурсивно собираем всех потомков
        $this->collectDescendantIds($category, $categoryIds);
        
        $minPrice = $request->input('price_min');
        $maxPrice = $request->input('price_max');

        $brands = $request->input('brands', []);

        // Фильтруем продукты по всем категориям
        $query = Product::with(['category', 'images', 'specificationsB'])
            ->where(function($query) use ($categoryIds, $brands, $minPrice, $maxPrice) {
                $query->where(function($subQuery) use ($categoryIds) {
                    $subQuery->whereIn('category_id', $categoryIds);
                });

                // Добавляем фильтр по цене, если заданы min и max
                if (!is_null($minPrice) && !is_null($maxPrice)) {
                    $query->whereBetween('price', [$minPrice, $maxPrice]);
                }

                // Фильтр по брендам
                if (!empty($brands)) {
                    $query->whereIn('brand', (array)$brands);
                }
            });
            
        // Применяем сортировку
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
        }
        
        $products = $query->paginate($limit, ['*'], 'page', $page);
        
        if($additionData){
            $categoryProducts = Product::where('category_id', $category->id)->get();
            $brandsList = $categoryProducts->pluck('brand')->filter()->unique()->values();
            // Минимальная и максимальная цена
            $minPriceValue = $categoryProducts->min('price');
            $maxPriceValue = $categoryProducts->max('price');
        }
        

        // Форматируем данные для каждого продукта
        $formattedProducts = collect($products->items())->map(function($product) {
            $images = $this->transformProductImages($product);
            
            $count = $product->quantity;
            if($count == 0){
                $count = 'Нет в наличии';
            } else if($count > 5){
                $count = 'Много';
            } else {
                $count = 'В наличии';
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'old_price' => $product->old_price,
                'description' => $product->description,
                'short_description' => $product->short_description,
                'in_stock' => (bool)$product->in_stock,
                'rating' => $product->rating,
                'brand' => $product->brand,
                'article' => $product->article,
                'slug' => $product->slug,
                'type' => $product->type,
                'count' => $count,
                'specificationsB' => $product->specificationsB,
                // 'category' => $product->category ? [
                //     'id' => $product->category->id,
                //     'name' => $product->category->name,
                //     'title' => $product->category->title,
                //     'slug' => $product->category->slug,
                //     'image_url' => $product->category->image_url,
                //     'description_image_url' => $product->category->description_image_url,
                // ] : null,
                'images' => $images['images'],
                'main_image' => $images['main_image'],
            ];
        });


        $category->parents = json_decode($category->getPath());

        $categoryResponse = [
            'id' => $category->id,
            'name' => $category->name,
            'title' => $category->title,
            'slug' => $category->slug,
            'description' => $category->description,
            'image_url' => $category->image_url,
            'description_image_url' => $category->description_image_url,
            'parents' => $category->parents,

        ];

        if ($additionData) {
            $categoryResponse['brands'] = $brandsList;
            $categoryResponse['min_price'] = $minPriceValue;
            $categoryResponse['max_price'] = $maxPriceValue;
        }
        
        return response()->json([
            'category' => $categoryResponse,
            'products' => $formattedProducts,
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more' => $products->hasMorePages(),
            ],
        ]);
    }

    // public function paginateProducts(Request $request): JsonResponse
    // {
        
    //     $request->validate([
    //         'page' => 'nullable|integer|min:1',
    //         'per_page' => 'nullable|integer|min:1|max:100',
    //         'sort' => 'nullable|string|in:price_asc,price_desc,newest,popular',
    //         'filters' => 'nullable|array',
    //     ]);

    //     $page = $request->input('page', 1);
    //     $perPage = $request->input('per_page', 2);
    //     $sort = $request->input('sort', 'newest');
    //     $filters = $request->input('filters', []);

    //     $query = Product::with(['category', 'images'])
    //         ->when(isset($filters['category_id']), function($q) use ($filters) {
    //             $categoryId = $filters['category_id'];
    //             $category = Category::find($categoryId);
                
    //             if ($category) {
    //                 $categoryIds = [$category->id];
    //                 $this->collectDescendantIds($category, $categoryIds);
    //                 $q->whereIn('category_id', $categoryIds);
    //             }
    //         })
    //         ->when(isset($filters['price_min']) && isset($filters['price_max']), function($q) use ($filters) {
    //             $q->whereBetween('price', [$filters['price_min'], $filters['price_max']]);
    //         })
    //         ->when(isset($filters['brands']), function($q) use ($filters) {
    //             $q->whereIn('brand', (array)$filters['brands']);
    //         })
    //         ->when(isset($filters['search']), function($q) use ($filters) {
    //             $q->where('name', 'like', '%'.$filters['search'].'%');
    //         });

    //     switch ($sort) {
    //         case 'price_asc':
    //             $query->orderBy('price', 'asc');
    //             break;
    //         case 'price_desc':
    //             $query->orderBy('price', 'desc');
    //             break;
    //         case 'newest':
    //             $query->orderBy('created_at', 'desc');
    //             break;
    //         case 'popular':
    //             $query->orderBy('views', 'desc');
    //             break;
    //     }

    //     $products = $query->paginate($perPage, ['*'], 'page', $page);

    //     return response()->json([
    //         'data' => $products->items(),
    //         'meta' => [
    //             'current_page' => $products->currentPage(),
    //             'last_page' => $products->lastPage(),
    //             'per_page' => $products->perPage(),
    //             'total' => $products->total(),
    //             'has_more' => $products->hasMorePages(),
    //         ]
    //     ]);
    // }

    /**
     * Получить список категорий от родителя до продукта (путь категорий)
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function getCategoryPath(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->with('category')->first();
        
        if (!$product) {
            return response()->json(['message' => 'Продукт не найден'], 404);
        }
        
        if (!$product->category) {
            return response()->json(['message' => 'У продукта нет категории'], 404);
        }
        
        // Получаем путь категорий от корня до категории продукта
        $path = $product->category->getPath()->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug
            ];
        });
        
        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug
            ],
            'category_path' => $path
        ]);
    }
} 