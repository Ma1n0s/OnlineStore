<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Models\User;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

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

Route::middleware('auth:sanctum')->post('/purchase', [PurchaseController::class, 'processPurchase']);

// Authentication routes with CSRF protection disabled
Route::group(['middleware' => [ 'guest']], function() {
    // Запрос кода верификации по email
    Route::post('/auth/request-code', function(Request $request) {
        $request->validate([
            'email' => 'required|email'
        ]);

        $passwd = Str::random(10);
        echo $passwd;

        $user = User::where('email', $request->email)->first();
        
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
            'password'=> $passwd
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

// Маршруты для продуктов
Route::apiResource('products', ProductController::class);

// Маршруты для категорий
Route::apiResource('categories', CategoryController::class);
Route::get('categories/roots', [CategoryController::class, 'roots']);
Route::get('categories/{category}/children', [CategoryController::class, 'children']);
Route::get('categories/{category}/descendants', [CategoryController::class, 'descendants']);
Route::get('categories/{category}/ancestors', [CategoryController::class, 'ancestors']);
Route::get('categories/slug/{slug}', [CategoryController::class, 'getBySlug']);

// Получение всех пользователей
Route::get('/users', function() {
    return User::all();
});

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
