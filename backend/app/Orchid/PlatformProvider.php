<?php

declare(strict_types=1);

namespace App\Orchid;

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
        return [
            Menu::make('Products')
            ->icon('bag')
            ->route('platform.product.list')
            ->canSee(true), 
                
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
        ];
    }
}