<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Получить все категории с их дочерними категориями
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $onlyRoots = $request->query('roots', false);
        
        if ($onlyRoots) {
            // Получаем только корневые категории
            $categories = Category::whereNull('parent_id')->get();
        } else {
            // Получаем все категории с их дочерними элементами
            $categories = Category::with('children')->whereNull('parent_id')->get();
        }
        
        return response()->json($categories);
    }
    
    /**
     * Получить детальную информацию о категории с дочерними категориями
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        $category->load('children');
        
        return response()->json($category);
    }
    
    /**
     * Получить категорию по slug
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function getBySlug(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->first();
        
        if (!$category) {
            return response()->json(['message' => 'Категория не найдена'], 404);
        }
        
        $category->load('children');
        
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
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:categories',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['message' => 'Ошибка валидации', 'errors' => $validator->errors()], 422);
        }
        
        try {
            $categoryData = $request->only(['name', 'title', 'description', 'parent_id']);
            
            // Если title не указан, используем name
            if (!isset($categoryData['title']) || empty($categoryData['title'])) {
                $categoryData['title'] = $categoryData['name'];
            }
            
            // Если slug не указан, генерируем его из названия
            if (!$request->has('slug')) {
                $categoryData['slug'] = Str::slug($request->input('name'));
            } else {
                $categoryData['slug'] = $request->input('slug');
            }
            
            // Проверяем уникальность slug в рамках родительской категории
            if (isset($categoryData['parent_id'])) {
                $existingCategory = Category::where('parent_id', $categoryData['parent_id'])
                    ->where('slug', $categoryData['slug'])
                    ->first();
                    
                if ($existingCategory) {
                    return response()->json([
                        'message' => 'Ошибка валидации',
                        'errors' => ['slug' => ['Slug должен быть уникальным в рамках родительской категории']],
                    ], 422);
                }
            }
            
            // Обработка загрузки изображения категории
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
                $categoryData['image_url'] = '/storage/' . $imagePath;
            }
            
            // Обработка загрузки изображения описания
            if ($request->hasFile('description_image')) {
                $descImagePath = $request->file('description_image')->store('categories/descriptions', 'public');
                $categoryData['description_image_url'] = '/storage/' . $descImagePath;
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
            'name' => 'sometimes|required|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|boolean',
            'remove_description_image' => 'nullable|boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['message' => 'Ошибка валидации', 'errors' => $validator->errors()], 422);
        }
        
        try {
            $categoryData = $request->only(['name', 'title', 'description', 'parent_id']);
            
            // Если name изменился, а title не указан явно, обновляем title
            if ($request->has('name') && !$request->has('title') && $request->input('name') !== $category->name) {
                $categoryData['title'] = $request->input('name');
            }
            
            // Проверяем, не является ли родитель потомком данной категории
            if ($request->has('parent_id')) {
                $parentId = $request->input('parent_id');
                
                // Нельзя установить категорию своим родителем
                if ($parentId == $category->id) {
                    return response()->json([
                        'message' => 'Ошибка валидации',
                        'errors' => ['parent_id' => ['Категория не может быть своим родителем']],
                    ], 422);
                }
                
                // Проверяем, не является ли новый родитель потомком этой категории
                $parent = Category::find($parentId);
                $currentParent = $parent;
                
                while ($currentParent) {
                    if ($currentParent->id === $category->id) {
                        return response()->json([
                            'message' => 'Ошибка валидации',
                            'errors' => ['parent_id' => ['Дочерняя категория не может быть родителем']],
                        ], 422);
                    }
                    $currentParent = $currentParent->parent;
                }
            }
            
            // Если slug не указан, но название изменилось, генерируем новый slug
            if ($request->has('name') && $request->input('name') !== $category->name && !$request->has('slug')) {
                $categoryData['slug'] = Str::slug($request->input('name'));
            } elseif ($request->has('slug')) {
                $categoryData['slug'] = $request->input('slug');
            }
            
            // Проверяем уникальность slug в рамках родительской категории
            if (isset($categoryData['slug']) && 
                (isset($categoryData['parent_id']) || $category->parent_id)) {
                
                $parentId = $categoryData['parent_id'] ?? $category->parent_id;
                
                $existingCategory = Category::where('parent_id', $parentId)
                    ->where('slug', $categoryData['slug'])
                    ->where('id', '!=', $category->id)
                    ->first();
                    
                if ($existingCategory) {
                    return response()->json([
                        'message' => 'Ошибка валидации',
                        'errors' => ['slug' => ['Slug должен быть уникальным в рамках родительской категории']],
                    ], 422);
                }
            }
            
            // Обработка загрузки изображения категории
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
                $categoryData['image_url'] = '/storage/' . $imagePath;
            }
            
            // Удаление изображения если указан флаг
            if ($request->input('remove_image') && $category->image_url) {
                $this->removeImageFromStorage($category->image_url);
                $categoryData['image_url'] = null;
            }
            
            // Обработка загрузки изображения описания
            if ($request->hasFile('description_image')) {
                $descImagePath = $request->file('description_image')->store('categories/descriptions', 'public');
                $categoryData['description_image_url'] = '/storage/' . $descImagePath;
            }
            
            // Удаление изображения описания если указан флаг
            if ($request->input('remove_description_image') && $category->description_image_url) {
                $this->removeImageFromStorage($category->description_image_url);
                $categoryData['description_image_url'] = null;
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
            // Удаляем изображения, если они есть
            if ($category->image_url) {
                $this->removeImageFromStorage($category->image_url);
            }
            
            if ($category->description_image_url) {
                $this->removeImageFromStorage($category->description_image_url);
            }
            
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
     * Получить дочерние категории для указанной категории
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function children(Category $category): JsonResponse
    {
        return response()->json($category->children);
    }
    
    /**
     * Получить все потомки категории (все уровни)
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function descendants(Category $category): JsonResponse
    {
        $category->load('descendants');
        return response()->json($this->flattenDescendants($category));
    }
    
    /**
     * Получить предков категории (путь от корня)
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function ancestors(Category $category): JsonResponse
    {
        return response()->json($category->getPath());
    }
    
    /**
     * Получить плоский список всех потомков категории
     *
     * @param Category $category
     * @return array
     */
    private function flattenDescendants(Category $category): array
    {
        $result = [];
        
        foreach ($category->children as $child) {
            $result[] = $child;
            if ($child->children->count() > 0) {
                $result = array_merge($result, $this->flattenDescendants($child));
            }
        }
        
        return $result;
    }
    
    /**
     * Получить все корневые категории (без родителя)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function roots(Request $request): JsonResponse
    {
        $withChildren = $request->query('with_children', false);
        
        if ($withChildren) {
            $rootCategories = Category::with('children')->whereNull('parent_id')->get();
        } else {
            $rootCategories = Category::whereNull('parent_id')->get();
        }
        
        return response()->json($rootCategories);
    }
    
    /**
     * Удаляет изображение из хранилища
     * 
     * @param string $imageUrl
     * @return void
     */
    private function removeImageFromStorage(string $imageUrl): void
    {
        $path = str_replace('/storage/', '', $imageUrl);
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }
    }
} 