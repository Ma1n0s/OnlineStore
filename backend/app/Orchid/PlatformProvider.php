<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Screen\Actions\Menu as MenuItem;
use Orchid\Platform\ItemMenu;
use Illuminate\Support\Facades\Session;
use App\Models\Category;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Menus\CategoriesMenu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Новости')
                ->icon('pencil')
                ->route('platform.news.list')
                ->canSee(true),
            
            $this->buildCategoriesMenu(),
            
            Menu::make('Продукты')
                ->icon('bag')
                ->route('platform.product.list')
                ->permission('platform.products.view'),

            Menu::make('Свайпер')
                ->icon('bs.image')
                ->route('platform.swiper')
                ->canSee(true)
                ->title('Управление контентом'),

            // Menu::make('Заявки')
            //     ->icon('bs.card-list')
            //     ->route('platform.application.list')
            //     ->permission('platform.applications.view')
            //     ->title('Управление заявками'),

            Menu::make('Заказы')
                ->icon('bs.card-list')
                ->route('platform.order.list')
                // ->permission('platform.orders.view')
                ->title('Управление заказами'),

                
            // Menu::make('Карточка')
            //     ->icon('bs.card-text')
            //     ->route('platform.example.cards')
            //     ->divider(),

            Menu::make('Профили пользователей')
                ->icon('bs.person')
                ->route('platform.profiles.list')
                ->permission('platform.profiles.view')
                ->title('Управление профилями'),

             Menu::make('Обратная связь')
                ->icon('chat')
                ->route('platform.feedback.list')
                ->permission('platform.feedback.view')
                ->title('Управление обращениями'),

            // Menu::make(__('Пользователи'))
            //     ->icon('bs.people')
            //     ->route('platform.systems.users')
            //     ->permission('platform.systems.users')
            //     ->title(__('Контроль доступа')),

            // Menu::make(__('Роли'))
            //     ->icon('bs.shield')
            //     ->route('platform.systems.roles')
            //     ->permission('platform.systems.roles')
            //     ->divider(),
        ];
    }

    /**
     * Build the categories menu with infinite nesting
     */
    protected function buildCategoriesMenu(): Menu
    {
        return Menu::make('Категории')
            ->icon('folder')
            ->permission('platform.categories.view')
            ->route('platform.category.list')
            ->add([
                MenuItem::make('Все категории')
                    ->route('platform.category.list')
                    ->icon('list'),
                MenuItem::make('Создать корневую категорию')
                    ->route('platform.category.create')
                    ->icon('plus'),
            ])
            ->list($this->buildNestedCategoriesMenu());
    }

    /**
     * Recursively build nested categories menu
     */
    protected function buildNestedCategoriesMenu(?int $parentId = null): array
    {
        return Category::with('children')
            ->where('parent_id', $parentId)
            ->get()
            ->map(function (Category $category) {
                $menuItem = MenuItem::make($category->name)
                    ->route('platform.category.action', $category)
                    ->icon('folder');

                // Рекурсивно добавляем подкатегории
                if ($category->children->isNotEmpty()) {
                    $menuItem->list($this->buildNestedCategoriesMenu($category->id));
                }

                // Добавляем кнопку "Добавить подкатегорию"
                $menuItem->add([
                    MenuItem::make('Добавить подкатегорию')
                        ->route('platform.category.create', ['parent_id' => $category->id])
                        ->icon('plus'),
                ]);

                return $menuItem;
            })
            ->toArray();
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
                
            ItemPermission::group('Products')
                ->addPermission('platform.products.view', 'View products')
                ->addPermission('platform.products.create', 'Create products')
                ->addPermission('platform.products.edit', 'Edit products')
                ->addPermission('platform.products.delete', 'Delete products'),
                
            ItemPermission::group('Categories')
                ->addPermission('platform.categories.view', 'View categories')
                ->addPermission('platform.categories.create', 'Create categories')
                ->addPermission('platform.categories.edit', 'Edit categories')
                ->addPermission('platform.categories.delete', 'Delete categories'),

            ItemPermission::group('Новости')
                ->addPermission('platform.news.view', 'Просмотр новостей')
                ->addPermission('platform.news.create', 'Создание новостей')
                ->addPermission('platform.news.edit', 'Редактирование новостей')
                ->addPermission('platform.news.delete', 'Удаление новостей'),

            ItemPermission::group('Свайпер')
                ->addPermission('platform.swiper.view', 'Просмотр свайпера')
                ->addPermission('platform.swiper.create', 'Создание элементов свайпера')
                ->addPermission('platform.swiper.edit', 'Редактирование элементов свайпера')
                ->addPermission('platform.swiper.delete', 'Удаление элементов свайпера'),

            ItemPermission::group('Applications')
                ->addPermission('platform.applications.view', 'View applications')
                ->addPermission('platform.applications.create', 'Create applications')
                ->addPermission('platform.applications.edit', 'Edit applications')
                ->addPermission('platform.applications.delete', 'Delete applications'),

            ItemPermission::group('Профили')
                ->addPermission('platform.profiles.view', 'Просмотр профилей')
                ->addPermission('platform.profiles.edit', 'Редактирование профилей'),
                
            ItemPermission::group('Обратная связь')
                ->addPermission('platform.feedback.view', 'Просмотр обратной связи')
                ->addPermission('platform.feedback.edit', 'Редактирование обратной связи'), 

            ItemPermission::group('Orders')
                ->addPermission('platform.orders.view', 'View orders')
                ->addPermission('platform.orders.create', 'Create orders')
                ->addPermission('platform.orders.edit', 'Edit orders')
                ->addPermission('platform.orders.delete', 'Delete orders'),
        ];
    }
    
    /**
     * Register screens for application.
     *
     * @return string[]
     */
    public function registerScreens(): array
    {
        return [
            \App\Orchid\Screens\ProductScreen::class,
            \App\Orchid\Screens\ProductListScreen::class,
            \App\Orchid\Screens\NewsListScreen::class,
            \App\Orchid\Screens\NewsScreen::class,
            \App\Orchid\Screens\Category\CategoryListScreen::class,
            \App\Orchid\Screens\Category\CategoryEditScreen::class,
            \App\Orchid\Screens\Category\CategoryActionScreen::class,
            \App\Orchid\Screens\ApplicationListScreen::class,
            \App\Orchid\Screens\ApplicationEditScreen::class,
            \App\Orchid\Screens\ProfileListScreen::class,
            \App\Orchid\Screens\ProfileEditScreen::class,
            \App\Orchid\Screens\SwiperItemListScreen::class,
            \App\Orchid\Screens\SwiperItemEditScreen::class,
            \App\Orchid\Screens\FeedbackListScreen::class,
            \App\Orchid\Screens\FeedbackEditScreen::class,
            \App\Orchid\Screens\OrderListScreen::class,
            \App\Orchid\Screens\OrderEditScreen::class,
        ];
    }
}