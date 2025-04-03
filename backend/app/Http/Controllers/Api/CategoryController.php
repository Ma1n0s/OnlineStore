<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Получить все категории с подкатегориями
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::with('subcategories')->get();
        
        return response()->json($categories);
    }
    
    /**
     * Получить детальную информацию о категории с подкатегориями
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        $category->load('subcategories');
        
        return response()->json($category);
    }
    
    /**
     * Создать новую категорию
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:categories',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['message' => 'Ошибка валидации', 'errors' => $validator->errors()], 422);
        }
        
        try {
            $categoryData = $request->only(['name', 'description']);
            
            // Если slug не указан, генерируем его из названия
            if (!$request->has('slug')) {
                $categoryData['slug'] = Str::slug($request->input('name'));
            } else {
                $categoryData['slug'] = $request->input('slug');
            }
            
            $category = Category::create($categoryData);
            
            return response()->json([
                'id' => $category->id,
                'message' => 'Категория успешно создана',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при создании категории',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Обновить категорию
     *
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
        ]);
        
        if ($validator->fails()) {
            return response()->json(['message' => 'Ошибка валидации', 'errors' => $validator->errors()], 422);
        }
        
        try {
            $categoryData = $request->only(['name', 'description']);
            
            // Если slug не указан, но название изменилось, генерируем новый slug
            if ($request->has('name') && $request->input('name') !== $category->name && !$request->has('slug')) {
                $categoryData['slug'] = Str::slug($request->input('name'));
            } elseif ($request->has('slug')) {
                $categoryData['slug'] = $request->input('slug');
            }
            
            $category->update($categoryData);
            
            return response()->json([
                'message' => 'Категория успешно обновлена',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при обновлении категории',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Удалить категорию
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            $category->delete();
            
            return response()->json([
                'message' => 'Категория успешно удалена',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при удалении категории',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Получить подкатегории для указанной категории
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function subcategories(Category $category): JsonResponse
    {
        return response()->json($category->subcategories);
    }
    
    /**
     * Создать новую подкатегорию для указанной категории
     *
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
    public function storeSubcategory(Request $request, Category $category): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['message' => 'Ошибка валидации', 'errors' => $validator->errors()], 422);
        }
        
        try {
            $subcategoryData = $request->only(['name', 'description']);
            $subcategoryData['category_id'] = $category->id;
            
            // Если slug не указан, генерируем его из названия
            if (!$request->has('slug')) {
                $subcategoryData['slug'] = Str::slug($request->input('name'));
            } else {
                $subcategoryData['slug'] = $request->input('slug');
            }
            
            // Проверяем уникальность slug в рамках категории
            $existingSubcategory = Subcategory::where('category_id', $category->id)
                ->where('slug', $subcategoryData['slug'])
                ->first();
                
            if ($existingSubcategory) {
                return response()->json([
                    'message' => 'Ошибка валидации',
                    'errors' => ['slug' => ['Slug должен быть уникальным в рамках категории']],
                ], 422);
            }
            
            $subcategory = Subcategory::create($subcategoryData);
            
            return response()->json([
                'id' => $subcategory->id,
                'message' => 'Подкатегория успешно создана',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при создании подкатегории',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Обновить подкатегорию
     *
     * @param Request $request
     * @param Category $category
     * @param Subcategory $subcategory
     * @return JsonResponse
     */
    public function updateSubcategory(Request $request, Category $category, Subcategory $subcategory): JsonResponse
    {
        // Проверяем, что подкатегория принадлежит категории
        if ($subcategory->category_id !== $category->id) {
            return response()->json([
                'message' => 'Подкатегория не принадлежит указанной категории',
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['message' => 'Ошибка валидации', 'errors' => $validator->errors()], 422);
        }
        
        try {
            $subcategoryData = $request->only(['name', 'description']);
            
            // Если slug не указан, но название изменилось, генерируем новый slug
            if ($request->has('name') && $request->input('name') !== $subcategory->name && !$request->has('slug')) {
                $subcategoryData['slug'] = Str::slug($request->input('name'));
            } elseif ($request->has('slug')) {
                $subcategoryData['slug'] = $request->input('slug');
            }
            
            // Проверяем уникальность slug в рамках категории, если он изменился
            if (isset($subcategoryData['slug']) && $subcategoryData['slug'] !== $subcategory->slug) {
                $existingSubcategory = Subcategory::where('category_id', $category->id)
                    ->where('slug', $subcategoryData['slug'])
                    ->where('id', '!=', $subcategory->id)
                    ->first();
                    
                if ($existingSubcategory) {
                    return response()->json([
                        'message' => 'Ошибка валидации',
                        'errors' => ['slug' => ['Slug должен быть уникальным в рамках категории']],
                    ], 422);
                }
            }
            
            $subcategory->update($subcategoryData);
            
            return response()->json([
                'message' => 'Подкатегория успешно обновлена',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при обновлении подкатегории',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Удалить подкатегорию
     *
     * @param Category $category
     * @param Subcategory $subcategory
     * @return JsonResponse
     */
    public function destroySubcategory(Category $category, Subcategory $subcategory): JsonResponse
    {
        // Проверяем, что подкатегория принадлежит категории
        if ($subcategory->category_id !== $category->id) {
            return response()->json([
                'message' => 'Подкатегория не принадлежит указанной категории',
            ], 404);
        }
        
        try {
            $subcategory->delete();
            
            return response()->json([
                'message' => 'Подкатегория успешно удалена',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при удалении подкатегории',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
} 