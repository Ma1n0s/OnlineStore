<?php

namespace App\Orchid\Screens;

use App\Models\Order;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class OrderViewScreen extends Screen
{
    public $order;
    
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Order $order): array
    {
        $this->order = $order;
        
        return [
            'order' => $order,
            'products' => $order->products,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'OrderViewScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::view('admin.order-details', [
                'order' => $this->order,
            ]),
        ];
    }
}
