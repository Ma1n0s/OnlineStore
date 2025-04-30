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

    /**
     * Получить список всех продуктов
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = Product::with(['category', 'specificationCategories.specifications', 'images'])
            ->paginate(10);
        
        $categoryController = app('App\Http\Controllers\Api\CategoryController');
            
        return response()->json(
            $products->map(function($product) use ($categoryController) {
                // Transform category image paths if category exists
                if ($product->category) {
                    $product->category = $categoryController->transformImagesPaths($product->category);
                }
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'description' => $product->description,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                        'image_url' => $product->category->image_url,
                        'description_image_url' => $product->category->description_image_url,
                    ] : null,
                    'specifications' => $product->specificationCategories->mapWithKeys(function ($category) {
                        return [$category->name => $category->specifications->mapWithKeys(function ($spec) {
                            return [$spec->name => $spec->value];
                        })];
                    }),
                    'images' => $this->transformProductImages($product)['images'],
                    'main_image' => $this->transformProductImages($product)['main_image'],
                ];
            })
        );
    }

    /**
     * Получить продукт по slug
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function getBySlug(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->first();
        
        if (!$product) {
            return response()->json(['message' => 'Продукт не найден'], 404);
        }
        
        $product = $product->load(['specificationCategories.specifications', 'images', 'category']);

        // Transform category image paths if category exists
        $categoryController = app('App\Http\Controllers\Api\CategoryController');
        if ($product->category) {
            $product->category = $categoryController->transformImagesPaths($product->category);
        }

        // Получаем путь категорий от корня до категории продукта
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

        $response = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'old_price' => $product->old_price,
            'description' => $product->description,
            'short_description' => $product->short_description,
            'in_stock' => (bool)$product->in_stock,
            'is_featured' => (bool)$product->is_featured,
            'sku' => $product->sku,
            'barcode' => $product->barcode,
            'quantity' => $product->quantity,
            'rating' => $product->rating,
            'slug' => $product->slug,
            'brand' => $product->brand,
            'category' => $categoryPath,
            'specifications' => $product->specifications,
            'mainSpecifications' => $product->specificationsB,
            'advantages' => $product->advantages,
            // 'specifications' => $product->specificationCategories->mapWithKeys(function ($category) {
            //     return [$category->name => $category->specifications->mapWithKeys(function ($spec) {
            //         return [$spec->name => $spec->value];
            //     })];
            // }),
            'images' => $this->transformProductImages($product)['images'],
            'main_image' => $this->transformProductImages($product)['main_image'],
        ];
        
        return response()->json($response);
    }

    /**
     * Получить детальную информацию о продукте
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with(['specificationCategories.specifications', 'images', 'category'])->findOrFail($id);

        // Получаем путь категорий от корня до категории продукта
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

        $response = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'old_price' => $product->old_price,
            'description' => $product->description,
            'short_description' => $product->short_description,
            'in_stock' => (bool)$product->in_stock,
            'is_featured' => (bool)$product->is_featured,
            'sku' => $product->sku,
            'barcode' => $product->barcode,
            'quantity' => $product->quantity,
            'rating' => $product->rating,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
                'image_url' => $product->category->image_url,
                'description_image_url' => $product->category->description_image_url,
            ] : null,
            'category_path' => $categoryPath,
            'specifications' => $product->specificationCategories->mapWithKeys(function ($category) {
                return [$category->name => $category->specifications->mapWithKeys(function ($spec) {
                    return [$spec->name => $spec->value];
                })];
            }),
            'images' => $this->transformProductImages($product)['images'],
            'main_image' => $this->transformProductImages($product)['main_image'],
        ];
        
        return response()->json($response);
    }

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
    public function destroy(Product $product): JsonResponse
    {
        try {
            $product->delete();
            
            return response()->json([
                'message' => 'Продукт успешно удален',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при удалении продукта',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

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