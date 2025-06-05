<a href="#" data-bs-toggle="modal" data-bs-target="#orderProductsModal-{{ $order->id }}">
    {{ $count }} товаров
</a>

<div class="modal fade" id="orderProductsModal-{{ $order->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Товары в заказе #{{ $order->order_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.orders.products-list', ['products' => $products])
            </div>
        </div>
    </div>
</div>