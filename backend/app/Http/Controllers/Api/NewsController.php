<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->get()->map(function ($item) {
            // Трансформируем данные для API
            return [
                'id' => $item->id,
                'title' => $item->title,
                'image' => $item->image_url, // Используем аксессор image_url вместо прямого поля
                'description' => $item->description,
                'tags' => $item->tags,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });
        
        return response()->json($news);
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'image' => 'nullable|string|max:255',
    //         'description' => 'nullable',
    //         'is_special' => 'boolean',
    //         'tags' => 'nullable|array'
    //     ]);

    //     $news = News::create($validated);
    //     return response()->json([
    //         'id' => $news->id,
    //         'title' => $news->title,
    //         'image' => $news->image_url,
    //         'description' => $news->description,
    //         'tags' => $news->tags,
    //         'created_at' => $news->created_at,
    //         'updated_at' => $news->updated_at
    //     ], 201);
    // }

    // public function update(Request $request, News $news)
    // {
    //     $validated = $request->validate([
    //         'title' => 'sometimes|string|max:255',
    //         'image' => 'nullable|string|max:255',
    //         'description' => 'nullable',
    //         'is_special' => 'boolean',
    //         'tags' => 'nullable|array'
    //     ]);

    //     $news->update($validated);
    //     return response()->json([
    //         'id' => $news->id,
    //         'title' => $news->title,
    //         'image' => $news->image_url,
    //         'description' => $news->description,
    //         'tags' => $news->tags,
    //         'created_at' => $news->created_at,
    //         'updated_at' => $news->updated_at
    //     ]);
    // }

    // public function destroy(News $news)
    // {
    //     $news->delete();
    //     return response()->noContent();
    // }

    public function show($id)
    {
        $news = News::find($id);
        return response()->json([
            'id' => $news->id,
            'title' => $news->title,
            'image' => $news->image_url,
            'description' => $news->description,
            'tags' => $news->tags,
            'created_at' => $news->created_at,
            'updated_at' => $news->updated_at
        ]);
    }
}