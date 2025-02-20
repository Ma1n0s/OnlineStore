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
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-5">Доставка по Москве и области</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-100 p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-2">Время работы службы доставки</h2>
            <p>Пн–Вс 9:00–23:59</p>
        </div>
        
        <div class="bg-gray-100 p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-2">Способы оплаты</h2>
            <p>Оплата на сайте (для физических лиц) или оплата по счету (для юридических лиц)</p>
        </div>
    </div>
    <h1 class="text-3xl font-bold mb-5">Интервалы доставки</h1>
    <p class="mb-5">Вы можете выбрать удобный для вас интервал времени доставки заказа</p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 bg-gray-100 rounded shadow">
            <h2 class="text-xl font-semibold mb-3">В пределах МКАД</h2>
            
            <div class="mb-5">
                <h3 class="font-semibold">На следующий день</h3>
                <p>пн. - вс.:</p>
                <ul>
                    <li>09:00 - 15:00</li>
                    <li>09:00 - 17:00</li>
                    <li>10:00 - 15:00</li>
                </ul>
            </div>
            
            <div>
                <h3 class="font-semibold">В день заказа</h3>
                <p>пн. - пт.:</p>
                <ul>
                    <li>15:00 - 18:00</li>
                    <li>18:00 - 23:59</li>
                </ul>
                <p>сб. - вс.:</p>
                <ul>
                    <li>18:00 - 22:00</li>
                </ul>
            </div>
        </div>
        <div class="p-4 bg-gray-100 rounded shadow">
            <h2 class="text-xl font-semibold mb-3">За МКАД</h2>
            
            <div class="mb-5">
                <h3 class="font-semibold">На следующий день</h3>
                <p>пн. - вс.:</p>
                <ul>
                    <li>09:00 - 17:00</li>
                </ul>
            </div>
            
            <div>
                <h3 class="font-semibold">В день заказа</h3>
                <p>пн. - вс.:</p>
                <ul>
                    <li>18:00 - 23:59</li>
                </ul>
            </div>
        </div>
    </div>
    <div>
        <div class="flex flex-col w-full mt-2 hover:bg-gray-200 rounded">
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
    <h1 class="font-bold mb-2">Стоимость доставки, руб.</h1>
    <table class="min-w-full border-collapse bg-white text-left text-sm text-gray-500">
    <thead class="">
        <tr>
            <th class="px-6 py-3 border-b font-medium text-gray-700">Общий вес заказа</th>
            <th class="px-6 py-3 border-b font-medium text-gray-700">Внутри МКАД</th>
            <th class="px-6 py-3 border-b font-medium text-gray-700">От 0 до 15 км</th>
            <th class="px-6 py-3 border-b font-medium text-gray-700">От 16 до 35 км</th>
            <th class="px-6 py-3 border-b font-medium text-gray-700">От 36 до 60 км</th>
            <th class="px-6 py-3 border-b font-medium text-gray-700">От 61 до 100 км</th>
            <th class="px-6 py-3 border-b font-medium text-gray-700">От 101 до 130 км</th>
        </tr>
    </thead>
    <tbody>
        <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">от 0 до 14.9 кг</td>
            <td class="px-6 py-4">290</td>
            <td class="px-6 py-4">290</td>
            <td class="px-6 py-4">390</td>
            <td class="px-6 py-4">650</td>
            <td class="px-6 py-4">750</td>
            <td class="px-6 py-4">1000</td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">от 15 до 29.9 кг</td>
            <td class="px-6 py-4">540</td>
            <td class="px-6 py-4">540</td>
            <td class="px-6 py-4">640</td>
            <td class="px-6 py-4">900</td>
            <td class="px-6 py-4">1000</td>
            <td class="px-6 py-4">1250</td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">от 30 до 99.9 кг</td>
            <td class="px-6 py-4">780</td>
            <td class="px-6 py-4">780</td>
            <td class="px-6 py-4">1500</td>
            <td class="px-6 py-4">1800</td>
            <td class="px-6 py-4">2100</td>
            <td class="px-6 py-4">2400</td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">от 100 до 199.9 кг</td>
            <td class="px-6 py-4">1200</td>
            <td class="px-6 py-4">1400</td>
            <td class="px-6 py-4">1700</td>
            <td class="px-6 py-4">2000</td>
            <td class="px-6 py-4">2300</td>
            <td class="px-6 py-4">2600</td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">от 200 до 499.9 кг</td>
            <td class="px-6 py-4">1450</td>
            <td class="px-6 py-4">1650</td>
            <td class="px-6 py-4">1950</td>
            <td class="px-6 py-4">2250</td>
            <td class="px-6 py-4">2550</td>
            <td class="px-6 py-4">2850</td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">от 500 до 999.9 кг</td>
            <td class="px-6 py-4">1700</td>
            <td class="px-6 py-4">1900</td>
            <td class="px-6 py-4">2200</td>
            <td class="px-6 py-4">2500</td>
            <td class="px-6 py-4">2800</td>
            <td class="px-6 py-4">3100</td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">от 1000 до 1499.9 кг</td>
            <td class="px-6 py-4">9400</td>
            <td class="px-6 py-4">9800</td>
            <td class="px-6 py-4">10400</td>
            <td class="px-6 py-4">11100</td>
            <td class="px-6 py-4">12300</td>
            <td class="px-6 py-4">13200</td>
        </tr>
    </tbody>
</table>


</div>

</template>