<template>
    <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
        <nav class="flex space-x-2 text-gray-600 mb-4">
            <RouterLink to="" class="hover:underline">Главная</RouterLink>
            <p>/</p>
            <RouterLink to="" class="font-semibold">Инструменты</RouterLink>
            <p>/</p>
            <RouterLink to="" class="font-semibold">Шуруповерты</RouterLink>
        </nav>
        <div class="flex items-center space-x-2">
            <h2 class="font-bold text-2xl">Шуруповерты</h2>
            <p>4945 товаров</p>
        </div>
        <div class="grid grid-cols-6 gap-x-4 gap-y-2"> 
            <div class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-2 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                <div class="font-bold">Шуроповерты</div>
                <p class="text-gray-400 text-sm">4 754 товара</p>
                <img src="/public/15.webp" alt="alt" class=" "/>
            </div>
            <div class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-2 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                <div class="font-bold">Шуроповерты</div>
                <p class="text-gray-400 text-sm">4 754 товара</p>
                <img src="/public/15.webp" alt="alt" class=" "/>
            </div>
            <div class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-2 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                <div class="font-bold">Шуроповерты</div>
                <p class="text-gray-400 text-sm">4 754 товара</p>
                <img src="/public/15.webp" alt="alt" class=" "/>
            </div>
            <div class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-2 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                <div class="font-bold">Шуроповерты</div>
                <p class="text-gray-400 text-sm">4 754 товара</p>
                <img src="/public/15.webp" alt="alt" class=" "/>
            </div>
        </div>

        <div class="flex flex-col md:flex-row p-4">
            <p class="flex-1 font-bold mr-2">Найдено 4945 товаров</p>
            <div class="flex space-x-2 flex-grow items-center">
                <p>Сортировать по:</p>
                <button class="px-4 py-2">По популярности</button>
                <button class="px-4 py-2">Отзывам</button>
                <button class="px-4 py-2">Рейтингу</button>
                <button class="px-4 py-2">Цене</button>
                <div class="flex justify-end w-full">
                    <button>
                        <img src="/public/four-squares-button-of-view-options.svg" alt="" class="w-5 h-5 mr-2 filter text-red-500" />
                    </button>
                    <button>
                        <img src="/public/interface-design-structure-outline.svg" alt="" class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div class="bg-white shadow-md rounded-xl border-2 border-gray-200 overflow-hidden w-60">
                <div class="p-4">
                    <div class="flex">
                        <p>код: 15640682</p>
                        <img src="" alt="" class=""/>
                    </div>
                    <img src="" alt="Дрель-шуруповерт Ryobi ONE+" class="w-full h-40 object-cover">
                    <h3 class="mt-2 text-xl font-semibold">Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348</h3>
                    <p class="text-green-500">В наличии > 100 шт.</p>
                   
                    <div class="flex flex-col space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">Самовывоз:</span>
                            <span class="flex-1 text-right">сегодня</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Курьером:</span>
                            <span class="flex-1 text-right">сегодня</span>
                        </div>
                    </div>
                    <span class="line-through text-gray-400 mr-2">15 990 ₽</span>
                    <span class="bg-green-600 w-10 h-10 p-1 rounded-l-sm text-white items-center">-17%</span>
                    <span class="flex text-red-500">13 290 ₽</span>
                    <button class="flex bg-red-600 text-white py-2 px-4 rounded mt-2 hover:bg-red-700">В корзину</button>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-xl border-2 border-gray-200 overflow-hidden w-60">
                <div class="p-4">
                    <div class="flex">
                        <p>код: 15640682</p>
                        <img src="" alt="" class=""/>
                    </div>
                    <img src="" alt="Дрель-шуруповерт Ryobi ONE+" class="w-full h-40 object-cover">
                    <h3 class="mt-2 text-xl font-semibold">Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348</h3>
                    <p class="text-green-500">В наличии > 100 шт.</p>
                   
                    <div class="flex flex-col space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">Самовывоз:</span>
                            <span class="flex-1 text-right">сегодня</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Курьером:</span>
                            <span class="flex-1 text-right">сегодня</span>
                        </div>
                    </div>
                    <span class="line-through text-gray-400 mr-2">15 990 ₽</span>
                    <span class="bg-green-600 w-10 h-10 p-1 rounded-l-sm text-white items-center">-17%</span>
                    <span class="flex text-red-500">13 290 ₽</span>
                    <button class="flex bg-red-600 text-white py-2 px-4 rounded mt-2 hover:bg-red-700">В корзину</button>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-xl border-2 border-gray-200 overflow-hidden w-60">
                <div class="p-4">
                    <div class="flex">
                        <p>код: 15640682</p>
                        <img src="" alt="" class=""/>
                    </div>
                    <img src="" alt="Дрель-шуруповерт Ryobi ONE+" class="w-full h-40 object-cover">
                    <h3 class="mt-2 text-xl font-semibold">Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348</h3>
                    <p class="text-green-500">В наличии > 100 шт.</p>
                   
                    <div class="flex flex-col space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">Самовывоз:</span>
                            <span class="flex-1 text-right">сегодня</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Курьером:</span>
                            <span class="flex-1 text-right">сегодня</span>
                        </div>
                    </div>
                    <span class="line-through text-gray-400 mr-2">15 990 ₽</span>
                    <span class="bg-green-600 w-10 h-10 p-1 rounded-l-sm text-white items-center">-17%</span>
                    <span class="flex text-red-500">13 290 ₽</span>
                    <button class="flex bg-red-600 text-white py-2 px-4 rounded mt-2 hover:bg-red-700">В корзину</button>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-xl border-2 border-gray-200 overflow-hidden w-60">
                <div class="p-4">
                    <div class="flex">
                        <p>код: 15640682</p>
                        <img src="" alt="" class=""/>
                    </div>
                    <img src="" alt="Дрель-шуруповерт Ryobi ONE+" class="w-full h-40 object-cover">
                    <h3 class="mt-2 text-xl font-semibold">Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348</h3>
                    <p class="text-green-500">В наличии > 100 шт.</p>
                   
                    <div class="flex flex-col space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">Самовывоз:</span>
                            <span class="flex-1 text-right">сегодня</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Курьером:</span>
                            <span class="flex-1 text-right">сегодня</span>
                        </div>
                    </div>
                    <span class="line-through text-gray-400 mr-2">15 990 ₽</span>
                    <span class="bg-green-600 w-10 h-10 p-1 rounded-l-sm text-white items-center">-17%</span>
                    <span class="flex text-red-500">13 290 ₽</span>
                    <button class="flex bg-red-600 text-white py-2 px-4 rounded mt-2 hover:bg-red-700">В корзину</button>
                </div>
            </div>
            <!-- Повторите блок div для других товаров -->
        </div>




    </div>
</template>