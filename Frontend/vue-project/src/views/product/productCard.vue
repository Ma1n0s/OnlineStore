<script setup>
import { ref, onMounted } from 'vue';

const specifications = ref([]);
const features = ref([]);
const advantages = ref([]);
const description = ref([]);
const mainImage = ref("path/to/your/main/image.jpg");
const images = ref([
  "path/to/your/image1.jpg",
  "path/to/your/image2.jpg",
  "path/to/your/image3.jpg",
  "path/to/your/image4.jpg",
]);

// const tempSpecifications = [
//     { parameter: 'Тип двигателя', value: 'бесщёточный' },
//     { parameter: 'Макс крутящий момент', value: '55 Нм' },
//     { parameter: 'Тип аккумулятора', value: 'Li-Ion' },
//     { parameter: 'Напряжение аккумулятора', value: '18 В' },
//     { parameter: 'Емкость аккумулятора', value: '2 Ач' },
//     { parameter: 'Количество аккумуляторов в комплекте', value: '2' },
//     { parameter: 'Наличие удара', value: 'нет' },
//     { parameter: 'Наличие реверса', value: 'да' },
//     { parameter: 'Тормоз двигателя', value: 'есть' },
//     { parameter: 'Размер зажимаемой оснастки', value: '2-13 мм' },
// ];
const fetchSpecifications = async () => {
    try {
        const response = await fetch('/api/product-specifications');
        const data = await response.json();
        // specifications.value = tempSpecifications;
        specifications.value = {
            maxTorque: data.max_torque,
            batteryType: data.battery_type,
            batteryVoltage: data.battery_voltage,
            maxDrillDiameterMetal: data.max_diameter_metal,
            maxDrillDiameterWood: data.max_diameter_wood,
            chargerIncluded: data.charger_included ? 'да' : 'нет',
            netWeight: data.net_weight
        };
        advantages.value = {
            NameDescription: data.NameDescription,
            maxDescription1: data.maxDescription1,
            maxDescription2: data.maxDescription2,
            maxDescription3: data.maxDescription3,
            maxDescription4: data.maxDescription4,
            maxDescription5: data.maxDescription5,
        };
    } catch (error) {
        console.error('Ошибка при загрузке данных:', error);
    }
};
const fetchdescription = async () => {
    try {
        const response = await fetch('/api/product-description');
        description.value = await response.json();
        // specifications.value = tempSpecifications;
    } catch (error) {
        console.error('Ошибка при загрузке данных:', error);
    }
};

defineProps();



function checkScroll() {
            const productCard = document.getElementById('product-card');
            const windowHeight = window.innerHeight;
            const cardPosition = productCard.getBoundingClientRect().top;

            if (cardPosition < windowHeight / 2) {
                productCard.classList.remove('hidden');
            } else {
                productCard.classList.add('hidden');
            }
        }

        window.addEventListener('scroll', checkScroll);

onMounted(() => {
    fetchSpecifications();
    fetchdescription();
});       
</script>
<template>
    <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
        <nav class="flex flex-wrap space-x-2 text-gray-600 mb-4">
            <RouterLink to="" class="hover:underline">Главная</RouterLink>
            <p>/</p>
            <RouterLink to="" class="font-semibold">Инструменты</RouterLink>
            <p>/</p>
            <RouterLink to="" class="font-semibold">Шуруповерты</RouterLink>
            <p>/</p>
            <RouterLink to="" class="font-semibold">Аккумуляторные дрели-шуруповерты</RouterLink>  
            <p>/</p>
            <RouterLink to="" class="font-semibold">Безударные</RouterLink>
            <p>/</p>
            <RouterLink to="" class="font-semibold">KEYANG</RouterLink>
        </nav>

        <div>
            <h2 class="font-bold text-2xl mb-2">Бесщеточный аккумуляторный шуруповерт KEYANG DD18BL-W (Set)</h2>
        </div>

        <div class="flex justify-center mb-4">
            <img src="" alt="Шуруповерт KEYANG" class="w-full max-w-xs" />
        </div>
            <div class="flex space-x-4">
                <p class="text-sm">Код товара: 24955389</p>
                <div class="flex items-center space-x-2">
                    <img src="" alt="Отзыв" class="w-4 h-4" />
                    <p class="text-sm">14 отзывов | 5 вопросов</p>
                </div>
                <div class="flex items-center space-x-2">
                    <img src="" alt="Гарантия" class="w-4 h-4" />
                    <p class="text-sm">Гарантия производителя 1 год</p>
                </div>
                <div class="flex items-center space-x-2">
                    <img src="" alt="Избранное" class="w-4 h-4" />
                    <p class="text-sm">В избранное</p>
                </div>
            </div>

        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-8 p-4 md:p-8">

            <div class="flex flex-col space-y-4 w-full md:w-1/3">
                <div class="w-full">
                    <img :src="mainImage" alt="Product" class="object-cover w-full h-64 rounded-lg" />
                </div>
                <div class="flex space-x-2 overflow-x-auto">
                    <img v-for="image in images" :src="image" :key="image" 
                        class="cursor-pointer w-16 h-16 object-cover rounded-lg" 
                        @click="mainImage = image" />
                </div>
            </div>

            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-800">Сезон впереди</h1>
                <!-- <p class="text-sm text-gray-600">{{ promotionCode }}</p> -->
                <div class="mt-4">
                    <h2 class="text-lg font-semibold">Макс крутящий момент: {{ specifications.maxTorque }}</h2>
                    <p>Тип аккумулятора: {{ specifications.batteryType }}</p>
                    <p>Напряжение аккумулятора: {{ specifications.batteryVoltage }}</p>
                    <p>Макс диаметр сверления (металл): {{ specifications.maxDrillDiameterMetal }}</p>
                    <p>Макс диаметр сверления (дерево): {{ specifications.maxDrillDiameterWood }}</p>
                    <p>Заряжающее устройство в комплекте: {{ specifications.chargerIncluded }}</p>
                    <p>Вес нетто: {{ specifications.netWeight }}</p>
                </div>
            </div>

            <div class="w-full md:w-1/4 bg-white border rounded-lg p-4 shadow-md">
                <h2 class="text-xl font-bold  text-red-600">Сезон впереди</h2>
                <div class="flex justify-between mt-2">
                    <span class="line-through text-gray-500">23 990 ₽</span>
                    <span class="text-green-600">Выгода 2 000 ₽</span>
                </div>
                <h2 class="text-2xl font-bold text-black">21 990 ₽</h2>
                <div class="mt-4">
                    <span>5498 ₽ x 4 платежа в рассрочку</span>
                </div>
                <button class="mt-4 w-full bg-red-600 text-white py-2 rounded-lg">В корзину</button>
                <button class="mt-4 w-full bg-slate-200  text-black py-2 rounded-lg">Быстрый заказ</button>
            </div>
        </div>


        <div class="border-b">
            <nav class="flex flex-col md:flex-row space-x-0 md:space-x-4 py-4">
                <router-link
                    to="#description"
                    class="text-gray-600 hover:text-black mb-2 md:mb-0"
                    active-class="border-b-2 border-black"
                >
                    ОПИСАНИЕ И ХАРАКТЕРИСТИКИ
                </router-link>
                
            </nav>
        </div>
        <div id="description" class="my-8">
            <div class="flex flex-col md:flex-row">
                <!-- flex-1 -->
                <div class="p-6 text-gray-800 text-sm">
                    <h2 v-if="features.length" class="text-xl font-semibold mb-2">
                    Инструмент обладает оптимальным набором функций для эффективной работы:
                    </h2>
                    <ul v-if="features.length" class="list-disc list-inside mb-4">
                        <li v-for="(feature, index) in features" :key="index">{{ feature }}</li>
                    </ul>
                    <div>
                        <p class="font-bold text-lg">Технические характеристики KEYANG DD18BL-W (Set)</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full " id="description">
                            <thead>
                            <tr class="">
                                <th class="text-left py-2 px-4">Параметр</th>
                                <th class="text-left py-2 px-4">Значение</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="desc in description" :key="desc.parameter">
                                    <td class="py-2 px-4">{{ desc.parameter }}</td>
                                    <td class="py-2 px-4">{{ desc.value }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="max-w-2xl my-8 p-4 flex-1">
                        <h2 v-if="advantages.NameDescription" class="text-lg font-bold mb-4">{{ advantages.NameDescription }}</h2>
                        <ul class="list-disc list-inside space-y-2 text-sm">
                            <li v-if="advantages.maxDescription1" class="text-gray-700">{{ advantages.maxDescription1 }}</li>
                            <li v-if="advantages.maxDescription1" class="text-gray-700">{{ advantages.maxDescription2 }}</li>
                            <li v-if="advantages.maxDescription1" class="text-gray-700">{{ advantages.maxDescription3 }}</li>
                            <li v-if="advantages.maxDescription1" class="text-gray-700">{{ advantages.maxDescription4 }}</li>
                            <li v-if="advantages.maxDescription1" class="text-gray-700">{{ advantages.maxDescription5 }}</li>
                        </ul>
                    </div>
                </div>
                <div class="flex-1 p-4 md:p-6">
                        <h1 class="text-xl font-bold mb-2">KEYANG</h1>
                        <p class="text-gray-500 mb-4">Все товары бренда</p>

                        <div class="flex items-center mb-4">
                            <img src="" alt="Южная Корея" class="w-5 h-5 mr-2" />
                            <span class="text-gray-700">Южная Корея — родина бренда</span>
                        </div>
                        <div class="flex items-center mb-4">
                            <img src="" alt="Китай" class="w-5 h-5 mr-2" />
                            <span class="text-gray-700">Китай — страна производства</span>
                        </div>

                        <h2 class="text-lg font-semibold mt-4 mb-2">Комплектация</h2>
                        <ul class="list-disc ml-6 mb-4">
                            <li>Бесщёточный аккумуляторный шуруповерт</li>
                            <li>2 аккумулятора BL18045A (18V, 2.0Ah)</li>
                            <li>Зарядное устройство C14415</li>
                            <li>Руководство по эксплуатации</li>
                            <li>Крепление на ремень</li>
                            <li>Пластиковый кейс</li>
                        </ul>

                        <h2 class="text-lg font-semibold mt-4 mb-2">Информация об упаковке</h2>
                        <p class="text-gray-700 mb-2">Единица товара: Штука</p>
                        <p class="text-gray-700">Вес, кг: 4.15</p>
                        <p class="text-gray-700">Длина, мм: 407</p>
                        <p class="text-gray-700">Ширина, мм: 321</p>
                        <p class="text-gray-700">Высота, мм: 123</p>

                        <h2 class="text-lg font-semibold mt-4 mb-2">Документация</h2>
                        <ul class="list-none ml-0 mb-4">
                            <li><a href="#" class="text-blue-500 underline">Инструкция к товару</a></li>
                            <li><a href="#" class="text-blue-500 underline">Сертификаты соответствия</a></li>
                            <li><a href="#" class="text-blue-500 underline">Скачать всю документацию</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- <div class="spacer"></div> 
    
            <div id="product-card" class="hidden flex items-center justify-between p-4 border shadow-md bg-white">
                <div class="flex items-center">
                    <img src="" alt="Дрель" class="h-16 w-16 object-contain mr-4">
                    <div>
                        <p class="text-sm text-gray-500">Код товара: 24955389</p>
                        <p class="font-bold">Бессеточный аккумуляторный шуруповерт KEYANG DD18BL-W (Set)</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xl font-bold text-red-600">21 990 ₽</p>
                    <p class="line-through text-gray-500">23 990 ₽</p>
                    <button class="mt-2 bg-red-600 text-white px-4 py-2 ">В корзину</button>
                </div>
            </div>

            <div class="spacer"></div> -->
    </div>
</template>