<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <h5 class="card-header">Детали заказа #{{ $order->order_number }}</h5>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Дата:</th>
                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Статус:</th>
                            <td>{{ $order->status }}</td>
                        </tr>
                        <tr>
                            <th>Оплачен:</th>
                            <td>{{ $order->is_paid ? 'Да' : 'Нет' }}</td>
                        </tr>
                        <tr>
                            <th>Сумма:</th>
                            <td>{{ number_format($order->total_amount, 2, '.', ' ') }} ₽</td>
                        </tr>
                    </table>
                    
                    <h5 class="mt-4">Товары:</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Название</th>
                                <th>Цена</th>
                                <th>Количество</th>
                                <th>Сумма</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>{{ $product['name'] }}</td>
                                <td>{{ $product['price'] }} ₽</td>
                                <td>{{ $product['quantity'] }}</td>
                                <td>{{ $product['price'] * $product['quantity'] }} ₽</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>