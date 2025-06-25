<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderProductController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\SwiperController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\BonusController;
use App\Mail\VerificationCodeMail;

// Route::prefix('cart')->group(function () {
//     Route::get('/', [CartController::class, 'index']);
//     Route::post('/', [CartController::class, 'store']);
//     Route::delete('/{cartItem}', [CartController::class, 'destroy']);
//     Route::patch('/{cartItem}', [CartController::class, 'update']);
// });

Route::middleware('auth:sanctum')->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show']);
    Route::put('/', [ProfileController::class, 'update']);
    Route::put('/password', [ProfileController::class, 'updatePassword']);
    Route::put('/company', [ProfileController::class, 'updateCompany']);
    // Route::delete('/company', [ProfileController::class, 'deleteCompany']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load([
        'profile',
        'bonusTransactions',
        // 'orders' => function($query) {
        //     $query->with(['products' => function($q) {
        //         $q->select('products.id', 'products.name', 'products.image');
        //     }])->orderBy('created_at', 'desc');
        // }
    ]);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/bonus/transactions', [BonusController::class, 'getBonusTransactions']);
});

Route::middleware('auth:sanctum')->prefix('profile/companies')->group(function () {
    Route::get('/', [ProfileController::class, 'getCompanies']);
    Route::post('/', [ProfileController::class, 'addCompany']);
    Route::put('/{company}', [ProfileController::class, 'updateCompany']);
    Route::delete('/{company}', [ProfileController::class, 'deleteCompany']);
    Route::post('/{company}/set-main', [ProfileController::class, 'setMainCompany']);
});

// Route::get('/products/paginate', [ProductController::class, 'paginateProducts']);

Route::middleware('auth:sanctum')->post('/feedback', function (Request $request) {
    $request->validate([
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
        'rating' => 'nullable|integer|min:0|max:5',
        'contact_method' => 'nullable|string|in:email,phone',
    ]);

    $feedback = new \App\Models\Feedback([
        'user_id' => $request->user()->id,
        'subject' => $request->subject,
        'message' => $request->message,
        'rating' => $request->rating ?? 0,
        'contact_method' => $request->contact_method ?? 'email',
        'status' => 'new',
    ]);

    $feedback->save();

    return response()->json([
        'message' => 'Ваше обращение успешно отправлено!',
        'status' => 'success',
    ]);
});




// Authentication routes with CSRF protection disabled
Route::group(['middleware' => [ 'guest']], function() {
    // Запрос кода верификации по email
    Route::post('/auth/request-code', function(Request $request) {
        $request->validate([
            'email' => 'required|email'
        ]);

        $passwd = Str::random(10);
        $user = User::where('email', $request->email)->first();
        $verificationCode = "";
        if (!$user) {
            // Создаем нового пользователя
            $verificationCode = Str::random(6);
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($passwd), // Генерируем случайный пароль
                'verification_code' => $verificationCode,
                'name' => explode('@', $request->email)[0]
            ]);
        } else {
            // Генерируем новый код для существующего пользователя
            $verificationCode = Str::random(6);
            $user->verification_code = $verificationCode;
            $user->save();
        }
        
        // Отправляем код на email
        Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));

        return response()->json([
            'message' => 'Verification code sent to your email',
            'status' => 'pending_verification',
        ]);
    });

    // Верификация кода и авторизация
    Route::post('/auth/verify-code', function(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => 'error'
            ], 404);
        }

        if ($user->verification_code === $request->code) {
            $user->email_verified_at = now();
            $user->verification_code = null;
            $user->save();
            
            // Авторизуем пользователя через сессию
            auth()->login($user);
            
            return response()->json([
                'user' => $user,
                'status' => 'verified'
            ]);
        }
        
        return response()->json([
            'message' => 'Invalid verification code',
            'status' => 'error'
        ], 422);
    });

    // Авторизация по email и паролю
    Route::post('/auth/email-password', function(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'status' => 'error'
            ], 401);
        }

        // Авторизуем пользователя через сессию
        auth()->login($user);

        return response()->json([
            'user' => $user,
            'status' => 'success'
        ]);
    });
});

// Специальные маршруты для продуктов
Route::get('/products/by-category', [ProductController::class, 'getProductsByCategory']);
Route::get('/products/category/{category}', [ProductController::class, 'getProductsByCategoryId']);
Route::get('/products/slug/{slug}', [ProductController::class, 'getBySlug']);
Route::get('/products/category-slug/{slug}', [ProductController::class, 'getProductsByCategorySlug']);
Route::get('/products/{slug}/category-path', [ProductController::class, 'getCategoryPath']);

// Маршруты для продуктов
// Route::apiResource('products', ProductController::class);

// Маршруты для категорий
// Route::apiResource('categories', CategoryController::class);
Route::get('categories', [CategoryController::class, 'index']);


Route::get('categories/roots', [CategoryController::class, 'roots']);
Route::get('categories/{category}/children', [CategoryController::class, 'children']);
Route::get('categories/{category}/descendants', [CategoryController::class, 'descendants']);
Route::get('categories/{category}/ancestors', [CategoryController::class, 'ancestors']);
Route::get('categories/slug/{slug}', [CategoryController::class, 'getBySlug']);

// Маршрут для выхода из системы
Route::post('/auth/logout', function(Request $request) {
    if (auth()->check()) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
    
    return response()->json([
        'message' => 'Logged out successfully',
        'status' => 'success'
    ]);
});

// Маршруты для новостей
Route::apiResource('news', NewsController::class);

// Маршрут для свайпера
Route::get('/swiper', [SwiperController::class, 'index']);

// Маршрут для поиска
Route::get('/search', [ProductController::class, 'search']);

Route::get('/orders/active-cart', [OrderController::class, 'activeCart']);
Route::post('/orders/create-order', [OrderController::class, 'createOrderFromSelected']);
Route::post('/orders/use-bonus', [OrderController::class, 'updateOrderBonus']);
Route::post('/orders/company', [OrderController::class, 'updateOrderCompany']);
Route::post('/orders/{order}/message', [OrderController::class, 'updateMessageOrder']);

Route::middleware('auth:sanctum')->get('orders/full', action: [OrderProductController::class, 'index']);

Route::middleware('auth:sanctum')->prefix('orders/{order}/products')->group(function () {
    Route::post('/', action: [OrderProductController::class, 'store']);
    Route::post('/selected', action: [OrderProductController::class, 'updateAllSelected']);
    Route::put('/{product}', [OrderProductController::class, 'update']);
    Route::delete('/selected', action: [OrderProductController::class, 'deleteAllSelected']);
    Route::delete('/{product}', [OrderProductController::class, 'destroy']);
});

// Route::prefix('sync')->group(callback: function () {
//     Route::post('products-with-categories', action: [ProductController::class,'processProduct']);
// });

Route::post('sync/products-with-categories', action: [ProductController::class,'processProduct']);
Route::delete('sync/product/{article}', action: [ProductController::class,'destroy']);
Route::post('sync/order/{order}/update', action: [OrderController::class,'updateOrder']);
Route::post('/sync/order/{order}/set-order-id', action: [OrderController::class,'setProId']);
