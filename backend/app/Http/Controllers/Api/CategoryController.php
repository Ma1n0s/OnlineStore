<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Orchid\Attachment\Models\Attachment;
use Orchid\Attachment\File;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{


    function getRootParent(Category $category)
    {
        $current = $category;
        
        while ($current->parent) {
            $current = $current->parent;
        }
        
        return $current;
    }

    function hasProducts(Category $category): bool
    {
        return Cache::remember("category_{$category->id}_has_products", 3600, function() use ($category) {
            return $category->products()->exists();
        });
    }

    function getCategoryPath(Category $category)
    {
        $slugs = [];
        
        // Начинаем с текущей категории
        $current = $category;
        
        // Поднимаемся вверх по иерархии
        while ($current) {
            $slugs[] = $current->slug;
            $current = $current->parent;
        }
        
        // Разворачиваем массив, чтобы получить правильный порядок (от корня к текущей)
        $slugs = array_reverse($slugs);
        
        // Соединяем через /
        return implode('/', $slugs);
    }

    function getCategoryParents(Category $category)
    {
        $parents = collect();
        
        $currentCategory = $category;
        
        while ($currentCategory) {
            $parents->push($currentCategory);
            $currentCategory = $currentCategory->parent;
        }
        
        return $parents;
    }

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
        
        // Преобразуем ID изображений в полные пути
        $categories = $this->transformImagesInCategories($categories);
        
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
        
        // Преобразуем ID изображений в полные пути
        $category = $this->transformImagesPaths($category);
        
        // Преобразуем ID изображений для дочерних категорий
        if ($category->children && $category->children->count() > 0) {
            $category->children = $this->transformImagesInCategories($category->children);
        }
        
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
        
        // Преобразуем ID изображений в полные пути
        $category = $this->transformImagesPaths($category);
        

        foreach ($category->children as $child) {
            $child->haveProducts = count($child->children) == 0;
            $child = $this->transformImagesPaths($child);
        }

        $category->parents = json_decode($category->getPath());
        
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
            
            // Создаем категорию
            $category = Category::create($categoryData);
            
            // Обработка загрузки изображения категории через Orchid
            if ($request->hasFile('image')) {
                $file = new File($request->file('image'));
                $attachment = $file->load('categories');
                
                $category->image_url = $attachment->id;
                $category->save();
            }
            
            // Обработка загрузки изображения описания через Orchid
            if ($request->hasFile('description_image')) {
                $file = new File($request->file('description_image'));
                $attachment = $file->load('categories/descriptions');
                
                $category->description_image_url = $attachment->id;
                $category->save();
            }
            
            // Преобразуем ID изображений в полные пути перед возвратом
            $category = $this->transformImagesPaths($category);
            
            return response()->json([
                'id' => $category->id,
                'message' => 'Категория успешно создана',
                'category' => $category
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
            
            // Обработка загрузки изображения категории через Orchid
            if ($request->hasFile('image')) {
                // Удаляем старое изображение, если оно есть
                if ($category->image_url) {
                    $this->deleteAttachment($category->image_url);
                }
                
                // Загружаем новое изображение
                $file = new File($request->file('image'));
                $attachment = $file->load('categories');
                
                $categoryData['image_url'] = $attachment->id;
            }
            
            // Удаление изображения если указан флаг
            if ($request->input('remove_image') && $category->image_url) {
                $this->deleteAttachment($category->image_url);
                $categoryData['image_url'] = null;
            }
            
            // Обработка загрузки изображения описания через Orchid
            if ($request->hasFile('description_image')) {
                // Удаляем старое изображение, если оно есть
                if ($category->description_image_url) {
                    $this->deleteAttachment($category->description_image_url);
                }
                
                // Загружаем новое изображение
                $file = new File($request->file('description_image'));
                $attachment = $file->load('categories/descriptions');
                
                $categoryData['description_image_url'] = $attachment->id;
            }
            
            // Удаление изображения описания если указан флаг
            if ($request->input('remove_description_image') && $category->description_image_url) {
                $this->deleteAttachment($category->description_image_url);
                $categoryData['description_image_url'] = null;
            }
            
            $category->update($categoryData);
            
            // Преобразуем ID изображений в полные пути перед возвратом
            $category = $this->transformImagesPaths($category);
            
            return response()->json([
                'message' => 'Категория успешно обновлена',
                'category' => $category
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
                $this->deleteAttachment($category->image_url);
            }
            
            if ($category->description_image_url) {
                $this->deleteAttachment($category->description_image_url);
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
        $children = $category->children;
        $children = $this->transformImagesInCategories($children);
        return response()->json($children);
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
        $descendants = $this->flattenDescendants($category);
        $descendants = $this->transformImagesInCategories($descendants);
        return response()->json($descendants);
    }
    
    /**
     * Получить предков категории (путь от корня)
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function ancestors(Category $category): JsonResponse
    {
        $ancestors = $category->getPath();
        $ancestors = $this->transformImagesInCategories($ancestors);
        return response()->json($ancestors);
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
        
        $rootCategories = $this->transformImagesInCategories($rootCategories);
        
        return response()->json($rootCategories);
    }
    
    /**
     * Удаляет вложение по ID
     * 
     * @param string|int $attachmentId
     * @return void
     */
    private function deleteAttachment($attachmentId): void
    {
        $attachment = Attachment::find($attachmentId);
        
        if ($attachment) {
            $attachment->delete();
        }
    }
    
    /**
     * Преобразует ID изображений в полные пути для коллекции категорий
     * 
     * @param \Illuminate\Support\Collection|array $categories
     * @return \Illuminate\Support\Collection|array
     */
    public function transformImagesInCategories($categories)
    {
        if (is_array($categories)) {
            foreach ($categories as &$category) {
                $category = $this->transformImagesPaths($category);
            }
        } else {
            $categories->transform(function ($category) {
                return $this->transformImagesPaths($category);
            });
            
            // Также преобразуем изображения для дочерних категорий
            $categories->each(function ($category) {
                if ($category->children && $category->children->count() > 0) {
                    $category->children = $this->transformImagesInCategories($category->children);
                }
            });
        }
        
        return $categories;
    }
    
    /**
     * Преобразует ID изображений в полные пути для одной категории
     * Важно: преобразует image_url и description_image_url из ID вложений в URL изображений
     * 
     * @param Category $category
     * @return Category
     */
    public function transformImagesPaths(Category $category)
    {
        // Проверяем и преобразуем image_url
        if ($category->image_url) {
            $attachment = Attachment::find($category->image_url);
            if ($attachment) {
                $category->image_url = $attachment->url();
            }
        }
        
        // Проверяем и преобразуем description_image_url
        if ($category->description_image_url) {
            $attachment = Attachment::find($category->description_image_url);
            if ($attachment) {
                $category->description_image_url = $attachment->url();
            }
        }
        
        return $category;
    }
} 