<?php

declare(strict_types=1);

use App\Models\Category;
use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use App\Orchid\Screens\ProductScreen;
use App\Orchid\Screens\ProductListScreen;
use App\Orchid\Screens\ApplicationEditScreen;
use App\Orchid\Screens\ApplicationListScreen;
use App\Orchid\Screens\Category\CategoryActionScreen;
use App\Orchid\Screens\Category\CategoryEditScreen;
use App\Orchid\Screens\Category\CategoryListScreen;
use App\Orchid\Screens\ProfileListScreen;
use App\Orchid\Screens\ProfileEditScreen;
use App\Models\User;
use App\Models\Feedback;
use App\Orchid\Screens\NewsListScreen;
use App\Orchid\Screens\NewsScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;
use App\Orchid\Screens\SwiperItemListScreen;
use App\Orchid\Screens\SwiperItemEditScreen;
use App\Orchid\Screens\OrderViewScreen;
use App\Orchid\Screens\FeedbackListScreen;
use App\Orchid\Screens\FeedbackEditScreen;
use App\Orchid\Screens\OrderListScreen; 
use App\Orchid\Screens\OrderEditScreen;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/
// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->push('Главная'));

Route::screen('product/{product}/edit', ProductScreen::class)
    ->name('platform.product.edit');

Route::screen('product/create', ProductScreen::class)
    ->name('platform.product.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.product.list')
            ->push('Create');
    });

Route::screen('orders', OrderListScreen::class)
    ->name('platform.order.list');

Route::screen('orders/create', OrderEditScreen::class)
    ->name('platform.order.create');

Route::screen('orders/{order}/edit', OrderEditScreen::class)
    ->name('platform.order.edit');

Route::screen('news', NewsListScreen::class)->name('platform.news.list');
Route::screen('news/create', NewsScreen::class)->name('platform.news.create');
Route::screen('news/{news}/edit', NewsScreen::class)->name('platform.news.edit');

Route::screen('products', ProductListScreen::class)
    ->name('platform.product.list');

Route::screen('feedback', FeedbackListScreen::class)
    ->name('platform.feedback.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.main')
        ->push('Обратная связь'));

Route::screen('feedback/{feedback}/edit', FeedbackEditScreen::class)
    ->name('platform.feedback.edit')
    ->breadcrumbs(function (Trail $trail, Feedback $feedback) {
        return $trail
            ->parent('platform.feedback.list')
            ->push('Редактирование обращения');
    });

Route::screen('product/{product}/edit', ProductScreen::class)
    ->name('platform.product.edit');

Route::screen('product/create', ProductScreen::class)
    ->name('platform.product.create');

Route::screen('products', ProductListScreen::class)
    ->name('platform.product.list');

Route::screen('categories', CategoryListScreen::class)
    ->name('platform.category.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.main')
        ->push('Категории'));

Route::screen('categories/{category}', CategoryListScreen::class)
    ->name('platform.category.list.nested')
    ->breadcrumbs(function (Trail $trail, Category $category) {
        $trail->parent('platform.main')
              ->push('Категории', route('platform.category.list'));
        
        $current = $category;
        $ancestors = collect();
        
        while ($current->parent) {
            $ancestors->prepend($current->parent);
            $current = $current->parent;
        }
        
        foreach ($ancestors as $ancestor) {
            $trail->push($ancestor->name, route('platform.category.list.nested', $ancestor));
        }
        
        return $trail->push($category->name);
    });

Route::screen('category/create', CategoryEditScreen::class)
    ->name('platform.category.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.category.list')
        ->push('Создать категорию'));

Route::screen('category/{category}/edit', CategoryEditScreen::class)
    ->name('platform.category.edit')
    ->breadcrumbs(function (Trail $trail, Category $category) {
        $trail->parent('platform.main')
              ->push('Категории', route('platform.category.list'));
        
        $current = $category;
        $ancestors = collect();
        
        while ($current->parent) {
            $ancestors->prepend($current->parent);
            $current = $current->parent;
        }
        
        foreach ($ancestors as $ancestor) {
            $trail->push($ancestor->name, route('platform.category.list.nested', $ancestor));
        }
        
        return $trail->push('Редактировать: ' . $category->name);
    });

Route::screen('category/{category}/action', CategoryActionScreen::class)
    ->name('platform.category.action')
    ->breadcrumbs(function (Trail $trail, Category $category) {
        $trail->parent('platform.main')
              ->push('Категории', route('platform.category.list'));
        
        $current = $category;
        $ancestors = collect();
        
        while ($current->parent) {
            $ancestors->prepend($current->parent);
            $current = $current->parent;
        }
        
        foreach ($ancestors as $ancestor) {
            $trail->push($ancestor->name, route('platform.category.list.nested', $ancestor));
        }
        
        return $trail->push($category->name);
    });

Route::screen('applications', ApplicationListScreen::class)
    ->name('platform.application.list');

Route::screen('users/{user}/profile', UserProfileScreen::class)
    ->name('platform.systems.users.profile')
    ->breadcrumbs(function (Trail $trail, User $user) {
        return $trail
            ->parent('platform.systems.users')
            ->push('Профиль пользователя', route('platform.systems.users.profile', $user));
    });

Route::screen('applications/create', ApplicationEditScreen::class)
    ->name('platform.application.create');

Route::screen('applications/{application}/edit', ApplicationEditScreen::class)
    ->name('platform.application.edit');

    Route::screen('profiles', ProfileListScreen::class)
    ->name('platform.profiles.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.main')
        ->push('Профили пользователей'));

Route::screen('profiles/{user}/edit', ProfileEditScreen::class)
    ->name('platform.profiles.edit')
    ->breadcrumbs(function (Trail $trail, User $user) {
        return $trail
            ->parent('platform.profiles.list')
            ->push('Редактирование профиля', $user->name);
    });

Route::screen('orders/{order}', OrderViewScreen::class)
    ->name('platform.orders.view');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
// Route::screen('users', UserListScreen::class)
//     ->name('platform.systems.users')
//     ->breadcrumbs(fn (Trail $trail) => $trail
//         ->parent('platform.index')
//         ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
// Route::screen('roles', RoleListScreen::class)
//     ->name('platform.systems.roles')
//     ->breadcrumbs(fn (Trail $trail) => $trail
//         ->parent('platform.index')
//         ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');

// Route::screen('idea', Idea::class, 'platform.screens.idea');

// Swiper routes
Route::screen('swiper', SwiperItemListScreen::class)
    ->name('platform.swiper')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.main')
        ->push('Свайпер'));

Route::screen('swiper/create', SwiperItemEditScreen::class)
    ->name('platform.swiper.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.swiper')
        ->push('Создать элемент'));

Route::screen('swiper/{swiperItem}/edit', SwiperItemEditScreen::class)
    ->name('platform.swiper.edit')
    ->breadcrumbs(fn (Trail $trail, $swiperItem) => $trail
        ->parent('platform.swiper')
        ->push('Редактировать элемент'));
