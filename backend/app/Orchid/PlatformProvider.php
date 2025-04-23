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
     * @return Menu[(new \App\Orchid\Menus\CategoriesMenu())->build()]
     */
    public function menu(): array
    {
        $menu = [
            Menu::make('Категории')
                ->icon('folder')
                ->permission('platform.categories.view')
                ->title('Управление контентом')
                ->route('platform.category.list')
                ->add([
                    MenuItem::make('All Categories')
                        ->route('platform.category.list')
                        ->icon('list'),
                    MenuItem::make('Create Root Category')
                        ->route('platform.category.create')
                        ->icon('plus'),
                ])
                ->list(
                    $this->buildNestedCategoriesMenu()
                ),
                            
            Menu::make('Продукты')
                ->icon('bag')
                ->route('platform.product.list')
                ->permission('platform.products.view'),
                
            Menu::make('Карточка')
                ->icon('bs.card-text')
                ->route('platform.example.cards')
                ->divider(),

            Menu::make(__('Пользователи'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Контроль доступа')),

            Menu::make(__('Роли'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),
        ];

        // Добавляем категории в меню
        // $categories = Category::with('children')
        //     ->whereNull('parent_id')
        //     ->get();

        // foreach ($categories as $category) {
        //     $menu = $this->addCategoryToMenu($menu, $category);
        // }

        return $menu;
    }
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
                    MenuItem::make('Add Subcategory')
                        ->route('platform.category.create', ['parent_id' => $category->id])
                        ->icon('plus'),
                ]);

                return $menuItem;
            })
            ->toArray();
    }

    // public function registerMenu(): array
    // {
    //     return [
    //         // Другие пункты меню...
    //         (new \App\Orchid\Menus\CategoriesMenu())->build(),
    //     ];
    // }

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


    protected function buildCategoryItem(Category $category, int $level = 0): MenuItem
    {
        $indent = str_repeat('   ', $level);
        
        return MenuItem::make($indent . $category->name)
            ->route('platform.category.action', $category)
            ->icon($category->children->isNotEmpty() ? 'folder' : 'document')
            ->list(
                $category->children->map(function (Category $child) use ($level) {
                    return $this->buildCategoryItem($child, $level + 1);
                })->toArray()
            );
    }
        
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