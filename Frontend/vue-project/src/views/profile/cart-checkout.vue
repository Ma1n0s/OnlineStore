<script setup>
import { ref } from 'vue';
import Header from '../Header.vue';
import Footer from '../Footer.vue';

const selected = ref(false);
const quantity = ref(1);

const increment = () => {
  quantity.value++;
};

const decrement = () => {
  if (quantity.value > 1) quantity.value--;
};

</script>
<template>
    <Header/>
    <!-- <div class="bg-gray-200">
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
    </div> -->
    <div class="mx-auto bg-gray-200 px-4 md:px-6 lg:px-8 py-8 ">
        <div class="bg-white rounded-2xl w-hull h-36">
            <div class="flex  items-center">
                <h2 class="font-bold text-2xl mr-2">Корзина</h2>
                <p>2741-0895-29725</p>
            </div>
            <div class="container mx-auto p-4">
                <input
                    type="text"
                    placeholder="Быстрое добавление: введите наименование или код товара..."
                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                />
            </div>
            <div class="">
                <div class="flex -between">
                    <button class="text-blue-600 hover:text-blue-800">Выделить все</button>
                    <button class="text-red-600 hover:text-red-800">Удалить выбранные</button>
                </div>

                <div class="flex space-x-4">
                    <button class="text-blue-600 hover:text-blue-800">Поделиться</button>
                    <button class="text-blue-600 hover:text-blue-800">Скачать</button>
                    <button class="text-blue-600 hover:text-blue-800">Распечатать</button>
                </div>
            </div>
            <div class="">
                <input type="checkbox" v-model="selected" class="mr-4" />
                    <div class="flex-1">
                    <div class="text-gray-800 font-bold">VMESTE10</div>
                    <div class="text-gray-500">код: 16313057</div>
                <div class="text-gray-800">
                    Бесщеточная аккумуляторная дрель-шуруповерт AEG BS18S8L-202C 4935472277
                </div>
                <div class="text-xl font-semibold text-gray-800">25 790 ₽</div>
                <div class="flex items-center mt-2">
                    <button @click="decrement" class="bg-gray-200 rounded px-2">–</button>
                    <span class="mx-2">{{ quantity }}</span>
                    <button @click="increment" class="bg-gray-200 rounded px-2">+</button>
                </div>
                <div class="text-gray-500 mt-2">Можно забрать завтра</div>
                <div class="mt-4">
                    <span class="text-gray-800 font-semibold">Рекомендуем</span>
                    <span class="text-gray-400 ml-2">...</span>
                </div>
                </div>
                <button @click="favorite" class="ml-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M3 9h18m-6 6h6m-6 6h6" />
                </svg>
                </button>
            </div>
            <div class=" p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-4">Укажите данные получателя заказа</h2>
                
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Имя</label>
                    <input type="text" id="name" placeholder="Кирилл Филимонов" class="mt-1 p-2 w-full border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500" />
                </div>
                
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Телефон</label>
                    <input type="text" id="phone" placeholder="+7 (902) 446-47-87" class="mt-1 p-2 w-full border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500" />
                </div>
                
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="another-person" class="mr-2 leading-tight" />
                    <label for="another-person" class="text-sm text-gray-700">Заберет другой человек</label>
                </div>
                <div class="flex">
                    
                </div>
            </div>
        </div>
    </div>
</template>
