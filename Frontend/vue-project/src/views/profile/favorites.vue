<template>
    <div class="container mx-auto px-4 py-8">
        <nav class="flex space-x-4 text-gray-600 mb-4">
            <RouterLink to="" class="hover:underline">Главная /</RouterLink>
            <RouterLink to="" class="hover:underline">Личный кабинет</RouterLink>
            <span class="">/ Избранное</span>
        </nav>

        <h1 class="text-3xl font-bold mb-6">Избранное</h1>

        <div class="flex flex-col items-center">
            <div class="w-24 h-24 mb-4">
                <img src="https://upload.wikimedia.org/wikipedia/commons/4/4c/Heart_icon_red.svg" alt="Heart icon" class="w-full h-full" />
            </div>
            <p class="text-lg text-center mb-4">В избранном пока нет товаров</p>
            <p class="text-center mb-6">Жмите ❤️ на странице товара и добавляйте сюда то, что нравится. <br> Если цена на товар в избранном упадет — мы вам об этом сообщим.</p>
            <RouterLink to="" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Перейти на главную</RouterLink>
        </div>
    </div>
</template>