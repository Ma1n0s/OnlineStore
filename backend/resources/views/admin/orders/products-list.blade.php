<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Название</th>
                <th>Кол-во</th>
                <th>Стоимость</th>
                <th>Общая</th>
                <th>Тип</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->pivot->quantity }}</td>
                    <td>₽{{ number_format($product->pivot->price_at_order, 2) }}</td>
                    <td>₽{{ number_format($product->pivot->quantity * $product->pivot->price_at_order, 2) }}</td>
                    <td>
                        @php
                            $types = [
                                'preorder' => 'Под заказ',
                                'rent' => 'В аренду',
                                'instock' => 'В наличии',
                            ];
                        @endphp
                        {{ $types[$product->type] ?? $product->type }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>