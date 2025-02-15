<template>
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-baseline">
            <h1 class="text-2xl font-bold mr-2 mb-4">Шуроповерты</h1>
            <p class="text-gray-600 mb-6">4922 товара</p>
            <a href="#" class="text-red-600 hover:underline">Как выбрать шуроповерт</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold">Аккумуляторные</h2>
                <p class="text-gray-500">4 727 товаров</p>
                <img src="" alt="Аккумуляторные" class="w-full h-32 object-cover mb-4 rounded">
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold">Сетевые</h2>
                <p class="text-gray-500">362 товара</p>
                <img src="" alt="Сетевые" class="w-full h-32 object-cover mb-4 rounded">
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold">Винтоверты</h2>
                <p class="text-gray-500">320 товаров</p>
                <img src="" alt="Винтоверты" class="w-full h-32 object-cover mb-4 rounded">
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold">Ленточные</h2>
                <p class="text-gray-500">33 товара</p>
                <img src="" alt="Ленточные" class="w-full h-32 object-cover mb-4 rounded">
            </div>
        </div>
        <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-semibold">Найдено 4922 товара</h1>
                <div class="flex items-center">
                    <span class="mr-2">Сортировать по:</span>
                    <select class="border rounded p-1 mr-4">
                        <option>По популярности</option>
                        <option>Отзывы</option>
                        <option>Рейтингу</option>
                        <option>Цене</option>
                    </select>
                    <button class="p-2 bg-gray-300 rounded hover:bg-gray-400">
                        <svg class="w-5 h-5 text-gray-600" viewBox="0 0 20 20"><path d="M3 2a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM2 8a1 1 0 011-1h14a1 1 0 110 2H3a1 1 0 01-1-1zM3 14a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" fill="currentColor"/></svg>
                    </button>
                    <button class="ml-2 p-2 bg-red-600 rounded text-white hover:bg-red-700">
                        <svg class="w-5 h-5" viewBox="0 0 20 20"><path d="M3 3h14a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V5a2 2 0 012-2zM12 6v8l5-4-5-4z" fill="currentColor"/></svg>
                    </button>
                </div>
            </div>
        <hr class="border-gray-300"/>
        <div class="max-w-xs mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <img class="w-full h-48 object-cover" src="https://example.com/image.jpg" alt="Дрель-шурповерт">
            <div class="p-4">
                <h2 class="text-xl font-bold">Аккумуляторная дрель-шуруповерт Edon AD-21AUN 15423</h2>
                <div class="flex items-center my-2">
                    <span class="text-yellow-500 mr-1">★★★★★</span>
                    <span class="text-gray-600">(4)</span>
                </div>
                <p class="text-gray-700">Макс крутящий момент: 30 Нм</p>
                <p class="text-gray-700">Тип аккумулятора: Li-ion</p>
                <p class="text-gray-700">Напряжение аккумулятора: 21 В</p>
                <p class="text-gray-700">Макс диаметр сверления (металл): 10 мм</p>
                <p class="text-gray-700">Макс диаметр сверления (дерево): 22 мм</p>
                <p class="text-gray-700">Вес нетто: 1.1 кг</p>
                <p class="text-red-600 text-lg font-bold">5 120 ₽ <span class="line-through text-gray-500">6 360 ₽</span> <span class="text-green-500">-19%</span></p>
                <button class="mt-4 w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">В корзину</button>
            </div>
        </div>
    </div>

    <div class="max-w-xs bg-white p-4 rounded-lg shadow-md">
    <h2 class="text-xl font-semibold mb-4">Категории</h2>
    
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Инструмент</label>
        <input type="text" placeholder="Шуроповерты" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Наличие в магазинах</label>
        <select class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <option>Выберите магазин</option>
        </select>
    </div>

    <div class="mb-4">
        <input type="checkbox" id="today" class="mr-2 leading-tight">
        <label for="today" class="text-sm text-gray-700">Забрать сегодня</label>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Цена, ₽</label>
        <div class="flex space-x-2">
            <input type="text" placeholder="от 740" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <input type="text" placeholder="до 176 444" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>
    </div>

    <div class="flex items-center mb-4">
        <input type="range" class="w-full h-2 bg-red-500 border border-gray-300 rounded-lg" min="740" max="176444" />
    </div>
</div>



</template>