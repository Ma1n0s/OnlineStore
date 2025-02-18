<script setup lang="ts">
import { ref } from 'vue';
import { shallowRef } from 'vue';
import type { YMap } from '@yandex/ymaps3-types';
import Header from './Header.vue';
import Footer from './Footer.vue';

import {
  YandexMap,
  YandexMapDefaultSchemeLayer,
  YandexMapDefaultFeaturesLayer,
  YandexMapDefaultMarker,
} from 'vue-yandex-maps';

const map = shallowRef<null | YMap>(null);

const isMkadInsideVisible = ref(false);
const isMkadOutsideVisible = ref(false);

const toggleMkadInside = () => {
  isMkadInsideVisible.value = !isMkadInsideVisible.value;
};

const toggleMkadOutside = () => {
  isMkadOutsideVisible.value = !isMkadOutsideVisible.value;
};

const mapState = ref({
  center: [55.751574, 37.573856], 
  zoom: 9, 
});

const markerCoords = ref([55.751574, 37.573856]);
const markerProperties = ref({
  balloonContent: 'Это Москва!',
});

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
    <div class="p-4">
        <div class="flex items-center">
            <button @click="toggleMkadInside" class="mt-2 px-4 py-2 rounded hover:bg-gray-400">
                Условие доставки
            </button>
            <img src="" alt="arrow" class="" />
        </div>
        <div v-if="isMkadInsideVisible" class="mt-4 p-4 bg-gray-100 border border-gray-300">
            <p>Это содержимое внутри МКАД.</p>
        </div>
        
        <div class="flex items-center">
            <button @click="toggleMkadOutside" class="mt-2 px-4 py-2 rounded hover:bg-gray-400">
                Памятка покупателя
            </button>
            <img src="" alt="arrow" class="" />
        </div>
        <div v-if="isMkadOutsideVisible" class="mt-4 p-4 bg-gray-100 border border-gray-300">
            <p>Это содержимое вне МКАД.</p>
        </div>
    </div>

    <yandex-map
      v-model="map"
      :settings="{
        location: {
          center: [37.617644, 55.755819],
          zoom: 9,
        },
      }"
      width="100%"
      height="500px"
  >
    <yandex-map-default-scheme-layer/>
    <yandex-map-default-features-layer/>
    <yandex-map-default-marker :settings="{ coordinates: [37.617644, 55.755819] }"/>
  </yandex-map>

</div>

</template>