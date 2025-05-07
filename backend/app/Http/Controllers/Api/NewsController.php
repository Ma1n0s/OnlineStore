<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $News = News::orderBy('sort_order')->get();
        return response()->json($News);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|string|max:255',
            'is_special' => 'boolean',
            'tags' => 'nullable|array',
            'sort_order' => 'integer'
        ]);

        $News = News::create($validated);
        return response()->json($News, 201);
    }

    public function update(Request $request, News $News)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|string|max:255',
            'is_special' => 'boolean',
            'tags' => 'nullable|array',
            'sort_order' => 'integer'
        ]);

        $News->update($validated);
        return response()->json($News);
    }

    public function destroy(News $News)
    {
        $News->delete();
        return response()->noContent();
    }
}