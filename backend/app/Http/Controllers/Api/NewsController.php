<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $News = News::latest()->get();
        return response()->json($News);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable',
            'is_special' => 'boolean',
            'tags' => 'nullable|array'
        ]);

        $News = News::create($validated);
        return response()->json($News, 201);
    }

    public function update(Request $request, News $News)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable',
            'is_special' => 'boolean',
            'tags' => 'nullable|array'
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