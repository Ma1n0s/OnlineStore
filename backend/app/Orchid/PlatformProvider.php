<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Models\Category;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
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
        $menu = [
            Menu::make('Categories')
                ->icon('folder')
                ->route('platform.category.list')
                ->permission('platform.categories.view')
                ->title('Content Management'),
                
            Menu::make('Products')
                ->icon('bag')
                ->route('platform.product.list')
                ->permission('platform.products.view'),
                
            Menu::make('Cards')
                ->icon('bs.card-text')
                ->route('platform.example.cards')
                ->divider(),

            Menu::make(__('Пользователи'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Роли'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),
        ];

        // Добавляем категории в меню
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->get();

        foreach ($categories as $category) {
            $menu = $this->addCategoryToMenu($menu, $category);
        }

        return $menu;
    }

    /**
     * Добавляет категорию и её подкатегории в меню
     *
     * @param array $menu
     * @param Category $category
     * @param int $level
     * @return array
     */
    protected function addCategoryToMenu(array $menu, Category $category, int $level = 0): array
    {
        $prefix = str_repeat('— ', $level);
        
        $menuItem = Menu::make($prefix . $category->name)
            ->route('platform.category.action', $category)
            ->icon('folder');

        // Если есть подкатегории, добавляем их как дочерние элементы
        if ($category->children->isNotEmpty()) {
            foreach ($category->children as $child) {
                $menuItem = $menuItem->add($this->createCategoryMenuItem($child, $level + 1));
            }
        }

        $menu[] = $menuItem;
        return $menu;
    }

    /**
     * Создает пункт меню для категории
     *
     * @param Category $category
     * @param int $level
     * @return Menu
     */
    protected function createCategoryMenuItem(Category $category, int $level = 0): Menu
    {
        $prefix = str_repeat('— ', $level);
        
        $menuItem = Menu::make($prefix . $category->name)
            ->route('platform.category.action', $category)
            ->icon('folder');

        // Рекурсивно добавляем подкатегории
        if ($category->children->isNotEmpty()) {
            foreach ($category->children as $child) {
                $menuItem = $menuItem->add($this->createCategoryMenuItem($child, $level + 1));
            }
        }

        return $menuItem;
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
            \App\Orchid\Screens\Category\CategoryListScreen::class,
            \App\Orchid\Screens\Category\CategoryEditScreen::class,
            \App\Orchid\Screens\Category\CategoryActionScreen::class,
        ];
    }
}