<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvantageResource;
use App\Models\Advantage;
use Illuminate\Http\Request;

class AdvantageController extends Controller
{
    public function index()
    {
        $advantages = Advantage::orderBy('sort_order')->get();
        return AdvantageResource::collection($advantages);
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

        $advantage = Advantage::create($validated);
        return new AdvantageResource($advantage);
    }

    public function update(Request $request, Advantage $advantage)
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

        $advantage->update($validated);
        return new AdvantageResource($advantage);
    }

    public function destroy(Advantage $advantage)
    {
        $advantage->delete();
        return response()->noContent();
    }
}