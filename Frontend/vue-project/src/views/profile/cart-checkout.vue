<script setup>
import Header from '../Header.vue';
import Footer from '../Footer.vue';

</script>
<template>
    <div class="bg-gray-200">
        <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
            <div class="flex flex-col mb-2 md:flex-row">
                <div class="flex-row bg-white p-8 rounded-lg shadow-lg  md:mr-6 ">
                    <h1 class="text-2xl font-bold mb-4">Ваша корзина пока пуста</h1>
                    <p class="text-gray-600 mb-6">
                        Акции, специальные предложения и обзоры самых интересных товаров на главной странице помогут вам определиться с выбором!
                    </p>
                    <label for="quick-add" class="block text-gray-700 mb-2">Быстрое добавление:</label>
                    <input 
                        type="text" 
                        id="quick-add" 
                        placeholder="введите наименование или код товара…" 
                        class="border border-gray-300 rounded-md p-2 w-full focus:outline-none focus:ring focus:ring-blue-500"
                    />
                </div>

                <div class="flex-1">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <p class="text-gray-600 mb-4">Для оформления заказа, выберите в корзине товары, которые хотите купить</p>
                        <h2 class="text-2xl font-bold mb-2">Итого</h2>
                        <p class="text-3xl font-semibold">0 ₽</p>
                        <button class="mt-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200 ">Оформить заказ</button>
                    </div>
                </div>
            </div>
            <div>
                <button class="bg-red-600 rounded-lg p-2 text-white">На главную</button>
            </div>
        </div>
    </div>
</template>
