<?php

namespace App\Orchid\Menus;

use App\Models\Category;
use Orchid\Platform\Menu\Menu;
use Orchid\Platform\Menu\MenuItem;

class CategoriesMenu
{
    public function build(): Menu
    {
        $menu = Menu::make('Categories')
            ->icon('folder')
            ->permission('platform.categories.view')
            ->title('Content Management');

        $categories = Category::with('children')->whereNull('parent_id')->get();
        
        foreach ($categories as $category) {
            $this->addCategoryToMenu($menu, $category);
        }

        return $menu;
    }

    protected function addCategoryToMenu(Menu $menu, Category $category, int $level = 0)
    {
        $indent = str_repeat('&nbsp;&nbsp;', $level);
        $menu->add(
            MenuItem::make($indent . $category->name)
                ->route('platform.category.action', $category)
                ->icon($category->children->isNotEmpty() ? 'folder' : 'document')
        );

        foreach ($category->children as $child) {
            $this->addCategoryToMenu($menu, $child, $level + 1);
        }
    }
}