<script setup>
import { ref } from 'vue';
import Header from '../Header.vue';
import Footer from '../Footer.vue';

const selected = ref(false);
const quantity = ref(1);

const fetchSpecifications = async () => {
    try {
        const response = await fetch('/api/cart-checkout');
        const data = await response.json();
        // specifications.value = tempSpecifications;
        specifications.value = {
            maxTorque: data.max_torque,
            batteryType: data.battery_type,
            batteryVoltage: data.battery_voltage,
            maxDrillDiameterMetal: data.max_diameter_metal,
            maxDrillDiameterWood: data.max_diameter_wood,
            netWeight: data.net_weight
        };
    } catch (error) {
        console.error('Ошибка при загрузке данных:', error);
    }
};

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
    <div class="mx-auto bg-gray-200 px-4 md:px-6 lg:px-8 py-8">
        <div class="flex space-x-6">
            <!-- Левый блок (основной контент) -->
            <div class="bg-white rounded-2xl w-3/4 p-6">
                <div class="flex items-center mb-4">
                    <h2 class="font-bold text-2xl mr-2">Корзина</h2>
                    <p>2741-0895-29725</p>
                </div>
                <div class="mb-4">
                    <input
                        type="text"
                        placeholder="Быстрое добавление: введите наименование или код товара..."
                        class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    />
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="select-all" name="select-all" class="mr-2"/>
                        <label for="select-all" class="text-gray-500 mr-2">Выделить все</label>
                        <img src="" alt="крест" class="mr-2"/>
                        <p class="text-gray-500">Удалить выбранные</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <img src="" alt="Поделиться" class="mr-2"/>
                            <p class="text-gray-500">Поделиться</p>
                        </div>
                        <div class="flex items-center">
                            <img src="" alt="Скачать" class="mr-2"/>
                            <p class="text-gray-500">Скачать</p>
                        </div>
                        <div class="flex items-center">
                            <img src="" alt="Распечатать" class="mr-2"/>
                            <p class="text-gray-500">Распечатать</p>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="select-item" name="select-item" class="mr-2"/>
                            <img src="" alt="Товар" class="w-16 h-16 mr-4"/>
                            <div>
                                <p class="font-bold">WMSITE10</p>
                                <p class="text-sm text-gray-500">код: 16313057</p>
                                <p class="text-sm">Бесщеточная аккумуляторная дрель-шуруповерт AEG BS18SBL-202C 4935472277</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <p class="text-lg font-bold mr-4">25 790 ₽</p>
                            <div class="flex items-center border border-gray-300 rounded">
                                <button class="px-3 py-1">–</button>
                                <span class="px-3 py-1 border-l border-r border-gray-300">1</span>
                                <button class="px-3 py-1">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Правый блок (дополнительный контент) -->
            <div class="bg-white rounded-2xl w-1/4 p-6">
                <p class="text-gray-700 mb-4">Для оформления заказа, выберите в корзине товары, которые хотите купить</p>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-2xl">Итого</h2>
                    <p class="font-bold text-2xl">0 ₽</p>
                </div>
                <button class="w-full bg-gray-200 text-gray-400 rounded-2xl p-4 ">
                    Оформить заказ
                </button>
            </div>
        </div>
    </div>
</template>
