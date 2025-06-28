<?php

namespace App\Listeners;

use App\Events\OrderApiProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class UpdateOrderFromApiResponse
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderApiProcessed $event)
    {
        try {
            $order = Order::findOrFail($event->orderId);
            
            $order->update([
                'pro_id' => $event->responseData['id'] ?? null,
            ]);
            
            Log::info('Order updated from API response', [
                'order_id' => $event->orderId,
                'pro_id' => $event->responseData['id'] ?? null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to update order from API response', [
                'order_id' => $event->orderId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
