<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SwiperItem;
use Illuminate\Http\Request;

class SwiperController extends Controller
{
    /**
     * Get swiper items
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $items = SwiperItem::orderBy('position')
            ->get()
            ->map(function ($item) {
                // Добавляем полный URL к изображению, если оно относительное
                if ($item->image && !str_starts_with($item->image, 'http')) {
                    $item->image = url($item->image);
                }
                return $item;
            });
            
        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }
}
