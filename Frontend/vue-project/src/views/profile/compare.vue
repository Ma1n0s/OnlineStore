<template>
    <div class="container mx-auto px-4 py-8">
        <nav class="flex space-x-4 text-gray-600 mb-4">
            <RouterLink to="" class="hover:underline">Главная</RouterLink>
            <RouterLink to="" class="font-semibold">/ Сравнение</RouterLink>
        </nav>

        <div class="flex flex-col items-start p-6 bg-white">
            <h1 class="text-3xl font-bold mb-2">Сравнение товаров</h1>
            <p class="text-gray-700 mb-4">В сравнении пока ничего нет</p>
            <RouterLink to="" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition">Перейти в каталог</RouterLink>
        </div>
    </div>
</template>