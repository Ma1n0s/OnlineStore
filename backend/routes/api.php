<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Специальные маршруты для продуктов
Route::get('/products/by-category', [ProductController::class, 'getProductsByCategory']);

// Маршруты для продуктов
Route::apiResource('products', ProductController::class);

// Маршруты для категорий
Route::apiResource('categories', CategoryController::class);

// Маршруты для подкатегорий
Route::get('categories/{category}/subcategories', [CategoryController::class, 'subcategories']);
Route::post('categories/{category}/subcategories', [CategoryController::class, 'storeSubcategory']);
Route::put('categories/{category}/subcategories/{subcategory}', [CategoryController::class, 'updateSubcategory']);
Route::delete('categories/{category}/subcategories/{subcategory}', [CategoryController::class, 'destroySubcategory']);
