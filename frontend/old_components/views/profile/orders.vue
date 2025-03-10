<template>
    <div class="container mx-auto px-4 py-8">
        <div class="flex">
            <div class="w-1/4 bg-white p-4 shadow-lg rounded-2xl">
                <h2 class="font-semibold mb-4">Мой кабинет</h2>
                <ul>
                    <li class="mb-2"><RouterLink to="">Товары</RouterLink></li>
                    <li class="mb-2"><RouterLink to="">Корзина</RouterLink></li>
                    <li class="mb-2"><RouterLink to="">Избранное</RouterLink></li>
                    <li class="mb-2"><RouterLink to="">Просмотренные товары</RouterLink></li>
                    <li class="mb-2"><RouterLink to="">Сравнение</RouterLink></li>
                </ul>
                <h2 class="font-semibold mt-6 mb-4">Сервис и поддержка</h2>
                <ul>
                    <li><RouterLink to="">Помощь по работе с сайтом</RouterLink></li>
                </ul>
            </div>

            <div class="flex-grow bg-gray-700 rounded-2xl p-6">
                <h1 class="text-2xl font-bold mb-4">Войдите или зарегистрируйтесь</h1>
                <p class="mb-4">Личный кабинет даст возможность отслеживать состояние заказа. Через него можно получить контакты менеджера, курирующего ваш заказ.</p>
                <div class="flex space-x-4">
                    <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-500">Войти или зарегистрироваться</button>
                    <button class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Покупать как юрлицо</button>
                </div>
            </div>
        </div>
    </div>
</template>