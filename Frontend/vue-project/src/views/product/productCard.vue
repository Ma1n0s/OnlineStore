<script setup>
import { ref } from 'vue';

const mainImage = ref("path/to/your/main/image.jpg");
const images = ref([
  "path/to/your/image1.jpg",
  "path/to/your/image2.jpg",
  "path/to/your/image3.jpg",
  "path/to/your/image4.jpg",
]);
const promotionCode = ref("VMESTE10");

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
                <p class="text-sm text-gray-600">{{ promotionCode }}</p>
                <div class="mt-4">
                    <h2 class="text-lg font-semibold">Макс крутящий момент: 55 Нм</h2>
                    <p>Тип аккумулятора: Li-Ion</p>
                    <p>Напряжение аккумулятора: 18 В</p>
                    <p>Макс диаметр сверления (металл): 13 мм</p>
                    <p>Макс диаметр сверления (дерево): 36 мм</p>
                    <p>Заряжающее устройство в комплекте: есть</p>
                    <p>Вес нетто: 1.1 кг</p>
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
                <button class="mt-4 w-full bg-red-500 text-white py-2 rounded-lg">Быстрый заказ</button>
                <div class="mt-4">
                    <span>Списите до 8 796 Р бонусами<br /> Начислим 219 бонусов</span>
                </div>
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
                    <p class="mb-4">
                        Бесщеточный аккумуляторный шуруповерт KEYANG DD18BL-W(Set) используется для сверления отверстий в металле и дереве, а также для 
                        закручивания крепежных элементов.
                    </p>
                    <h2 class="text-xl font-semibold mb-2">Инструмент обладает оптимальным набором функций для эффективной работы:</h2>
                    <ul class="list-disc list-inside mb-4">
                        <li>Быстрозажимной патрон ускоряет процесс замены оснастки.</li>
                        <li>Наличие реверса облегчает извлечение заклинившего сверла.</li>
                        <li>Высокий крутящий момент 55 Нм.</li>
                        <li>Двигатель с повышенной долговечностью и производительностью благодаря специальной двигательной технологии KEYANG.</li>
                        <li>Подсветка позволяет работать в слабоосвещенных местах.</li>
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
                            <tr>
                                <td class="py-2 px-4">Тип двигателя</td>
                                <td class="py-2 px-4">бесщёточный</td>
                            </tr>
                            <tr class="">
                                <td class="py-2 px-4">Макс крутящий момент</td>
                                <td class="py-2 px-4">55 Нм</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4">Жестк. вращ. момент</td>
                                <td class="py-2 px-4">55 Нм</td>
                            </tr>
                            <tr class="">
                                <td class=" px-4">Тип аккумулятора</td>
                                <td class=" px-4">Li-Ion</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Напряжение аккумулятора</td>
                                <td class=" px-4">18 В</td>
                            </tr>
                            <tr class="">
                                <td class=" px-4">Емкость аккумулятора</td>
                                <td class=" px-4">2 Ач</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Устройство аккумулятора</td>
                                <td class=" px-4">слидер</td>
                            </tr>
                            <tr class="">
                                <td class=" px-4">Количество аккумуляторов в комплекте</td>
                                <td class=" px-4">2</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Наличие удара</td>
                                <td class=" px-4">нет</td>
                            </tr>
                            <tr class="">
                                <td class=" px-4">Ленточные (магазинные)</td>
                                <td class=" px-4">нет</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Наличие реверса</td>
                                <td class=" px-4">да</td>
                            </tr>
                            <tr class="">
                                <td class=" px-4">Наличие подсветки</td>
                                <td class=" px-4">да</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Тормоз двигателя</td>
                                <td class=" px-4">есть</td>
                            </tr>
                            <tr class="">
                                <td class=" px-4">Тип патрона</td>
                                <td class=" px-4">быстрозажимной</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Крепление патрона</td>
                                <td class=" px-4">1/2</td>
                            </tr>
                            <tr class="">
                                <td class=" px-4">Блокировка шпинделя</td>
                                <td class=" px-4">да</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Размер зажимаемой оснастки</td>
                                <td class=" px-4">2-13 мм</td>
                            </tr>
                            <tr class="">
                                <td class=" px-4">Min размер оснастки</td>
                                <td class=" px-4">2 мм</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Max размер оснастки</td>
                                <td class=" px-4">13 мм</td>
                            </tr>
                            <tr class="">
                                <td class=" px-4">Число скоростей</td>
                                <td class=" px-4">2</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Частота вращения шпинделя</td>
                                <td class=" px-4">0-500/0-1800 об/мин</td>
                            </tr>
                            <tr class="">
                                <td class=" px-4">Макс частота вращения шпинделя</td>
                                <td class=" px-4">1800 об/мин</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Max диаметр сверления (металл)</td>
                                <td class=" px-4">13 мм</td>
                            </tr>
                            <tr class="">
                                <td class=" px-4">Max диаметр сверления (дерево)</td>
                                <td class=" px-4">36 мм</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Время заряда</td>
                                <td class=" px-4">1 ч</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Универсальный аккумулятор</td>
                                <td class=" px-4">нет</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Число ступеней крутоящего комента</td>
                                <td class=" px-4">16+1</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Зарядное устройство в комплекте</td>
                                <td class=" px-4">есть</td>
                            </tr>
                            <tr>
                                <td class=" px-4">Дополнительная рукоятка</td>
                                <td class=" px-4">нет</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="max-w-2xl my-8 p-4 flex-1">
                        <h2 class="text-lg font-bold mb-4">Преимущества KEYANG DD18BL-W (Set)</h2>
                        <ul class="list-disc list-inside space-y-2 text-sm">
                            <li class="text-gray-700">Двойная изоляция защищает от короткого замыкания.</li>
                            <li class="text-gray-700">Плавный пуск.</li>
                            <li class="text-gray-700">Простая и компактная конструкция.</li>
                            <li class="text-gray-700">Эргономичная рукоятка.</li>
                            <li class="text-gray-700">Индикатор заряда батареи.</li>
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