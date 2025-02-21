<script setup lang="ts">
  import { ref } from 'vue';
  import { shallowRef } from 'vue';
  import Header from './Header.vue';
  import Footer from './Footer.vue';
  import { GMapMap, GMapMarker } from '@fawmi/vue-google-maps';

  const isMkadInsideVisible = ref(false);
  const isMkadOutsideVisible = ref(false);
  const toggleMkadInside = () => {
    isMkadInsideVisible.value = !isMkadInsideVisible.value;
  };
  const toggleMkadOutside = () => {
    isMkadOutsideVisible.value = !isMkadOutsideVisible.value;
  };
</script>
<template>
    <Header />
<div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <h2 class="text-3xl font-bold mb-5">Доставка по Москве и области</h2>
    
    <div class="bg-gray-100  p-4 max-w-md  my-4 rounded-lg">
        <h2 class="text-xl font-bold">Режим работы на 23 февраля</h2>
        <p class="mt-2"><strong>Прием звонков</strong><br>23.02 - без изменений</p>
        <p class="mt-2"><strong>Магазин</strong><br>23.02 - с 09:00 до 18:00</p>
        <p class="mt-2"><strong>Доставка</strong><br>23.02 - без изменений</p>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div class="bg-gray-100 p-6 rounded-2xl shadow">
            <h2 class="text-xl font-semibold mb-2">Время работы службы доставки</h2>
            <p>Пн–Вс 9:00–23:59</p>
        </div>
        
        <div class="bg-gray-100 p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-2">Способы оплаты</h2>
            <p>Оплата на сайте (для физических лиц) или оплата по счету (для юридических лиц)</p>
        </div>
    </div>
    <h2 class="text-3xl font-bold mb-5">Интервалы доставки</h2>
    <p class="mb-5">Вы можете выбрать удобный для вас интервал времени доставки заказа</p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 bg-gray-100 rounded shadow">
            <h2 class="text-xl font-semibold mb-3">В пределах МКАД</h2>
            
            <div class="mb-5">
                <h3 class="font-semibold">На следующий день</h3>
                <p class="font-semibold">пн. - вс.:</p>
                <ul>
                    <li>09:00 - 15:00</li>
                    <li>09:00 - 17:00</li>
                    <li>09:00 – 18:00</li>
                    <li>10:00 – 15:00</li>
                    <li>10:00 – 17:00</li>
                    <li>10:00 – 18:00</li>
                    <li>11:00 – 15:00</li>
                    <li>12:00 – 16:00</li>
                    <li>13:00 – 17:00</li>
                    <li>13:00 – 18:00</li>
                    <li>14:00 – 18:00</li>
                    <li>18:00 – 22:00</li>
                    <li>18:00 – 23:59</li>
                    <li>19:00 – 23:00</li>
                    <li>20:00 – 23:59</li>
                </ul>
            </div>
            
            <div class="flex flex-col items-start">
                <h3 class="font-semibold">В день заказа</h3>
                <p class="font-semibold">пн. - пт.:</p>
                <ul>
                    <li>15:00 - 18:00</li>
                    <li>18:00 - 22:00</li>
                    <li>19:00 - 23:00</li>
                    <li>20:00 - 23:59</li>
                </ul>
                <p class="font-semibold">сб. - вс.:</p>
                <ul>
                    <li>18:00 - 22:00</li>
                    <li>18:00 - 23:59</li>
                    <li>19:00 - 23:00</li>
                    <li>20:00 - 23:59</li>
                </ul>
            </div>
        </div>
        <div class="p-4 bg-gray-100 rounded shadow">
            <h2 class="text-xl font-semibold mb-3">За МКАД</h2>
            
            <div class="mb-5">
                <h3 class="font-semibold">На следующий день</h3>
                <p class="font-semibold">пн. - вс.:</p>
                <ul>
                    <li>09:00 - 17:00</li>
                    <li>09:00 - 18:00</li>
                    <li>10:00 - 17:00</li>
                    <li>10:00 - 18:00</li>
                    <li>18:00 - 23:59</li>
                </ul>
            </div>
            
            <div>
                <h3 class="font-semibold">В день заказа</h3>
                <p class="font-semibold">пн. - вс.:</p>
                <ul>
                    <li>18:00 - 23:59</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div>
        <div class="flex flex-col w-full mt-2  hover:bg-gray-200 rounded">
            <hr class="border-gray-300" />
            <div class="flex items-center w-full">
                <button @click="toggleMkadInside" class="px-4 py-2 rounded w-full text-left bg-transparent border-none">
                    Условие доставки
                </button>
                <img src="/public/arrow-down.svg" alt="arrow" class="w-4 h-4 mr-2" />
            </div>
            <hr class="border-gray-300" />
        </div>
        <div v-if="isMkadInsideVisible" class="mt-4 p-4 bg-gray-100 border border-gray-300">
            <p>Это содержимое внутри МКАД.</p>
        </div>

        <div class="flex flex-col w-full mt-2 hover:bg-gray-200 rounded">
            <hr class="border-gray-300" />
            <div class="flex items-center w-full">
                <button @click="toggleMkadOutside" class="px-4 py-2 rounded w-full text-left bg-transparent border-none">
                    Памятка покупателя
                </button>
                <img src="/public/arrow-down.svg" alt="arrow" class="w-4 h-4 mr-2" />
            </div>
            <hr class="border-gray-300" />
        </div>
        <div v-if="isMkadOutsideVisible" class="mt-4 p-4 bg-gray-100 border border-gray-300">
            <p>Это содержимое внутри МКАД.</p>
        </div>
    </div>
    <!-- <div>
      <GMapMap
        :center="{ lat: 10.0, lng: 10.0 }"
        :zoom="7"
        style="width: 100%; height: 400px"
      >
        <GMapMarker :position="{ lat: 10.0, lng: 10.0 }" />
      </GMapMap>
    </div> -->
    <div class="overflow-x-auto">
    <h2 class="text-2xl font-bold mb-4">Стоимость доставки, руб.</h2>
    <table class="min-w-full border-collapse text-left text-sm">
        <thead>
            <tr>
                <th class="px-4 py-3 border-b font-medium text-gray-700">Общий вес заказа</th>
                <th class="px-4 py-3 border-b font-medium text-gray-700">Внутри МКАД</th>
                <th class="px-4 py-3 border-b font-medium text-gray-700 hidden md:table-cell">От 0 до 15 км</th>
                <th class="px-4 py-3 border-b font-medium text-gray-700 hidden md:table-cell">От 16 до 35 км</th>
                <th class="px-4 py-3 border-b font-medium text-gray-700 hidden md:table-cell">От 36 до 60 км</th>
                <th class="px-4 py-3 border-b font-medium text-gray-700 hidden md:table-cell">От 61 до 100 км</th>
                <th class="px-4 py-3 border-b font-medium text-gray-700 hidden md:table-cell">От 101 до 130 км</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-4">от 0 до 14.9 кг</td>
                <td class="px-4 py-4">290</td>
                <td class="px-4 py-4 hidden md:table-cell">290</td>
                <td class="px-4 py-4 hidden md:table-cell">390</td>
                <td class="px-4 py-4 hidden md:table-cell">650</td>
                <td class="px-4 py-4 hidden md:table-cell">750</td>
                <td class="px-4 py-4 hidden md:table-cell">1000</td>
            </tr>
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-4">от 15 до 29.9 кг</td>
                <td class="px-4 py-4">540</td>
                <td class="px-4 py-4 hidden md:table-cell">540</td>
                <td class="px-4 py-4 hidden md:table-cell">640</td>
                <td class="px-4 py-4 hidden md:table-cell">900</td>
                <td class="px-4 py-4 hidden md:table-cell">1000</td>
                <td class="px-4 py-4 hidden md:table-cell">1250</td>
            </tr>
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-4">от 30 до 99.9 кг</td>
                <td class="px-4 py-4">780</td>
                <td class="px-4 py-4 hidden md:table-cell">780</td>
                <td class="px-4 py-4 hidden md:table-cell">1500</td>
                <td class="px-4 py-4 hidden md:table-cell">1800</td>
                <td class="px-4 py-4 hidden md:table-cell">2100</td>
                <td class="px-4 py-4 hidden md:table-cell">2400</td>
            </tr>
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-4">от 100 до 199.9 кг</td>
                <td class="px-4 py-4">1200</td>
                <td class="px-4 py-4 hidden md:table-cell">1400</td>
                <td class="px-4 py-4 hidden md:table-cell">1700</td>
                <td class="px-4 py-4 hidden md:table-cell">2000</td>
                <td class="px-4 py-4 hidden md:table-cell">2300</td>
                <td class="px-4 py-4 hidden md:table-cell">2600</td>
            </tr>
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-4">от 200 до 499.9 кг</td>
                <td class="px-4 py-4">1450</td>
                <td class="px-4 py-4 hidden md:table-cell">1650</td>
                <td class="px-4 py-4 hidden md:table-cell">1950</td>
                <td class="px-4 py-4 hidden md:table-cell">2250</td>
                <td class="px-4 py-4 hidden md:table-cell">2550</td>
                <td class="px-4 py-4 hidden md:table-cell">2850</td>
            </tr>
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-4">от 500 до 999.9 кг</td>
                <td class="px-4 py-4">1700</td>
                <td class="px-4 py-4 hidden md:table-cell">1900</td>
                <td class="px-4 py-4 hidden md:table-cell">2200</td>
                <td class="px-4 py-4 hidden md:table-cell">2500</td>
                <td class="px-4 py-4 hidden md:table-cell">2800</td>
                <td class="px-4 py-4 hidden md:table-cell">3100</td>
            </tr>
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-4">от 1000 до 1499.9 кг</td>
                <td class="px-4 py-4">9400</td>
                <td class="px-4 py-4 hidden md:table-cell">9800</td>
                <td class="px-4 py-4 hidden md:table-cell">10400</td>
                <td class="px-4 py-4 hidden md:table-cell">11100</td>
                <td class="px-4 py-4 hidden md:table-cell">12300</td>
                <td class="px-4 py-4 hidden md:table-cell">13200</td>
            </tr>
        </tbody>
    </table>
</div>



    <div>
        <div>
            <h2 class="text-2xl font-bold mb-4">Подъем заказа</h2>
            <p class="text-gray-700 mb-4">Наш магазин предлагает дополнительные услуги подъема заказа на этаж. Подъем осуществляется на лифте либо пешком, но не выше 5-го этажа. Условия и стоимость услуги зависят от веса товара.
            </p>
            <p class="text-gray-700 text-sm">Если заказ имеет вес тяжелее 1000 кг - то в таком случае стоимость подъема согласовывается отдельно.</p>
        </div>
        <div>
            <h2 class="text-2xl font-bold mb-4">Подъём заказа</h2>
                <ul class="list-disc list-inside">
                    <li class="text-gray-700 mb-2">
                        Заказ весом до 20 кг – до квартиры бесплатно.
                    </li>
                    <li class="text-gray-700 mb-2">
                        Заказ весом от 20 до 30 кг – доставка до подъезда, подъем нашим курьером на лифте – 150 руб., если нет лифта – 50 руб./этаж.
                    </li>
                </ul>
            </div>
        </div>
    <div>
        <h2 class="text-2xl font-bold mb-4">От 30 до 1000 кг – подъем осуществляет грузчик</h2>
        <p >Услуги грузчика предоставляются только в пределах МКАД с 10:00 до 18:00 по будням. Оформить заявку на услугу можно до 15:00 дня, предшествующего дате доставки. Общий вес заказа не должен превышать 1000 кг, а вес каждого товара – 35 кг.</p>
    </div>
    <div class="w-1/2">
        <h2 class="text-2xl font-bold mb-4">Стоимость доставки, руб.</h2>
        <table class="min-w-full border-collapse text-left text-sm ">
            <thead class="">
                <tr>
                    <th class="px-6 py-3 border-b font-medium text-gray-700">Общий вес заказа</th>
                    <th class="px-6 py-3 border-b font-medium text-gray-700">Стоимость подъема, руб.</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">29,9</td>
                    <td class="px-6 py-4">150</td>
                </tr>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">99,9</td>
                    <td class="px-6 py-4">450</td>
                </tr>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">199</td>
                    <td class="px-6 py-4">900</td>
                </tr>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">1000</td>
                    <td class="px-6 py-4">1100</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="w-1/2 mb-4">
        <p>Стоимость услуг указана для подъема товара на лифте. Если подъем осуществляется по лестнице, к указанной стоимости прибавляется 50 руб. за каждый этаж.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div class="bg-gray-100 p-6 rounded-2xl shadow">
        <h2 class="text-lg font-semibold">Доставка транспортной компанией</h2>
        <p class="text-gray-700">Вы сможете заказать доставку транспортными компаниями «Почта России», «СДЭК».</p>
        <RouterLink to="" class="text-blue-600 hover:underline mt-2">Полные условия программы</RouterLink>
    </div>

    <div class="bg-gray-100 p-6 rounded-2xl shadow">
        <h2 class="text-lg font-semibold">Отследите ваш заказ</h2>
        <p class="text-gray-700">Воспользуйтесь проверкой статуса заказа. Если у вас возникли вопросы по срокам или стоимости доставки, обращайтесь к нашим менеджерам по телефону 8 800 550-37-70.</p>
        <RouterLink to="" class="text-blue-600 hover:underline mt-2">Проверить статус заказа</RouterLink>
        </div>
    </div>



</div>
<Footer />
</template>

<style>
    .list-disc li {
        list-style-type: none;
    }
    .list-disc li::before {
        content: "●"; 
        color: red;   
        font-size: 1.2rem; 
        margin-right: 0.5rem; 
    }
</style>