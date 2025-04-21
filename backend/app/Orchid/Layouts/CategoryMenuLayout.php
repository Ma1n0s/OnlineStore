<?php

namespace App\Orchid\Layouts;

use App\Models\Category;
use Orchid\Platform\Menu;
use Orchid\Platform\ItemMenu;

class CategoryMenuLayout
{
    public function compose()
    {
        return $this->buildMenu(Category::roots());
    }

    protected function buildMenu($categories, $prefix = '')
    {
        $menu = new Menu();
        
        foreach ($categories as $category) {
            $menu->add($prefix . $category->name, 
                route('platform.category.action', $category))
                ->icon('folder');
                
            if ($category->children->isNotEmpty()) {
                $menu = $this->buildMenu($category->children, $prefix . '— ', $menu);
            }
        }
        
        return $menu;
    }
}