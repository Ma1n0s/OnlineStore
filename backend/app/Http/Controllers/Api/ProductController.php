<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductImportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

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
        $products = Product::with(['category', 'specificationCategories.specifications', 'images'])
            ->paginate(10);
            
        return response()->json(
            $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'description' => $product->description,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                    ] : null,
                    'specifications' => $product->specificationCategories->mapWithKeys(function ($category) {
                        return [$category->name => $category->specifications->mapWithKeys(function ($spec) {
                            return [$spec->name => $spec->value];
                        })];
                    }),
                    'images' => $product->images->map(function($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->url,
                            'alt' => $image->alt,
                        ];
                    }),
                ];
            })
        );
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
            ] : null,
            'specifications' => $product->specificationCategories->mapWithKeys(function ($category) {
                return [$category->name => $category->specifications->mapWithKeys(function ($spec) {
                    return [$spec->name => $spec->value];
                })];
            }),
            'images' => $product->images->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'alt' => $image->alt,
                ];
            }),
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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        $product->price = $validated['price'];
        $product->description = $validated['description'] ?? '';
        $product->short_description = $validated['short_description'] ?? '';
        $product->in_stock = $validated['in_stock'] ?? true;
        $product->is_featured = $validated['is_featured'] ?? false;
        $product->sku = $validated['sku'] ?? '';
        $product->barcode = $validated['barcode'] ?? '';
        $product->quantity = $validated['quantity'] ?? 0;
        $product->old_price = $validated['old_price'] ?? 0;
        $product->rating = $validated['rating'] ?? 0;
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
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                    ] : null,
                    'images' => $product->images->map(function($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->url,
                            'alt' => $image->alt,
                        ];
                    }),
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
} 