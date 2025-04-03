<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductImportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected ProductImportService $productImportService;

    public function __construct(ProductImportService $productImportService)
    {
        $this->productImportService = $productImportService;
    }

    /**
     * Получить список всех продуктов
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = Product::with(['category', 'subcategory', 'specificationCategories.specifications', 'images'])
            ->latest()
            ->paginate(15)
            ->through(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                    ] : null,
                    'subcategory' => $product->subcategory ? [
                        'id' => $product->subcategory->id,
                        'name' => $product->subcategory->name,
                        'slug' => $product->subcategory->slug,
                    ] : null,
                    'specifications' => $product->specificationCategories->mapWithKeys(function ($category) {
                        return [$category->name => $category->specifications->mapWithKeys(function ($spec) {
                            return [$spec->name => $spec->value];
                        })];
                    }),
                    'images' => [
                        'market' => $product->images->where('source', 'market')->sortBy('position')->pluck('url')->toArray(),
                        'yandex' => $product->images->where('source', 'yandex')->sortBy('position')->pluck('url')->toArray(),
                    ],
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ];
            });
        
        return response()->json($products);
    }

    /**
     * Получить детальную информацию о продукте
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        $product->load(['specificationCategories.specifications', 'images', 'category', 'subcategory']);
        
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'search_market_url' => $product->search_market_url,
            'search_images_url' => $product->search_images_url,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'subcategory' => $product->subcategory ? [
                'id' => $product->subcategory->id,
                'name' => $product->subcategory->name,
                'slug' => $product->subcategory->slug,
            ] : null,
            'specifications' => $product->specificationCategories->mapWithKeys(function ($category) {
                return [$category->name => $category->specifications->mapWithKeys(function ($spec) {
                    return [$spec->name => $spec->value];
                })];
            }),
            'images' => [
                'market' => $product->images->where('source', 'market')->sortBy('position')->pluck('url')->toArray(),
                'yandex' => $product->images->where('source', 'yandex')->sortBy('position')->pluck('url')->toArray(),
            ],
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ]);
    }

    /**
     * Создать новый продукт из данных скрапера
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_url' => 'nullable|string|url',
            'search_market_url' => 'nullable|string|url',
            'search_images_url' => 'nullable|string|url',
            'specifications' => 'nullable|array',
            'images' => 'nullable|array',
            'images.market' => 'nullable|array',
            'images.yandex' => 'nullable|array',
            'created_at' => 'nullable|string',
            'category' => 'nullable|string',
            'subcategory' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Ошибка валидации', 'errors' => $validator->errors()], 422);
        }

        try {
            // Получаем название категории и подкатегории
            $categoryName = $request->input('category', 'Другое');
            $subcategoryName = $request->input('subcategory', 'Разное');
            
            // Находим или создаем категорию
            $category = \App\Models\Category::firstOrCreate(
                ['name' => $categoryName],
                [
                    'slug' => \Illuminate\Support\Str::slug($categoryName),
                    'description' => "Категория товаров: {$categoryName}"
                ]
            );
            
            // Находим или создаем подкатегорию
            $subcategory = \App\Models\Subcategory::firstOrCreate(
                [
                    'category_id' => $category->id,
                    'name' => $subcategoryName
                ],
                [
                    'slug' => \Illuminate\Support\Str::slug($subcategoryName),
                    'description' => "Подкатегория {$categoryName}: {$subcategoryName}"
                ]
            );
            
            // Подготовка данных в формат, который ожидает ProductImportService
            $data = [
                'название_товара' => $request->input('name'),
                'описание' => $request->input('description'),
                'спецификации' => $request->input('specifications', []),
                'изображения' => [
                    'маркет' => $request->input('images.market', []),
                    'картинки' => $request->input('images.yandex', []),
                ],
                'ссылки' => [
                    'товар' => $request->input('product_url'),
                    'поиск_маркет' => $request->input('search_market_url'),
                    'поиск_картинки' => $request->input('search_images_url'),
                ],
                'время_запроса' => $request->input('created_at', now()->format('Y-m-d H:i:s')),
                // Используем ID категории и подкатегории, которые уже определены в парсере
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id,
            ];

            $product = $this->productImportService->importProduct($data);

            return response()->json([
                'id' => $product->id,
                'category' => $product->category ? $product->category->name : null,
                'subcategory' => $product->subcategory ? $product->subcategory->name : null,
                'message' => 'Продукт успешно создан',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при создании продукта',
                'error' => $e->getMessage(),
            ], 500);
        }
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
        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|integer|exists:categories,id',
            'subcategory_id' => 'nullable|integer|exists:subcategories,id',
            'per_page' => 'nullable|integer|min:1|max:50',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Ошибка валидации', 'errors' => $validator->errors()], 422);
        }

        $query = Product::with(['category', 'subcategory', 'specificationCategories.specifications', 'images']);

        // Фильтрация по категории
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Фильтрация по подкатегории
        if ($request->has('subcategory_id')) {
            $query->where('subcategory_id', $request->input('subcategory_id'));
        }

        // Получаем количество элементов на странице (по умолчанию 15, максимум 50)
        $perPage = min($request->input('per_page', 15), 50);

        $products = $query->latest()
            ->paginate($perPage)
            ->through(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                    ] : null,
                    'subcategory' => $product->subcategory ? [
                        'id' => $product->subcategory->id,
                        'name' => $product->subcategory->name,
                        'slug' => $product->subcategory->slug,
                    ] : null,
                    'specifications' => $product->specificationCategories->mapWithKeys(function ($category) {
                        return [$category->name => $category->specifications->mapWithKeys(function ($spec) {
                            return [$spec->name => $spec->value];
                        })];
                    }),
                    'images' => [
                        'market' => $product->images->where('source', 'market')->sortBy('position')->pluck('url')->toArray(),
                        'yandex' => $product->images->where('source', 'yandex')->sortBy('position')->pluck('url')->toArray(),
                    ],
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ];
            });

        return response()->json($products);
    }
} 