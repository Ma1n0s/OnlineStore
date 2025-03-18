<template>
  <div class="relative w-full h-[500px] rounded-lg shadow-lg overflow-hidden">
    <!-- Карта -->
    <div id="map" class="w-full h-full"></div>

    <!-- Элементы управления -->
    <div class="absolute top-4 right-4 flex flex-col space-y-2">
      <button
        @click="zoomIn"
        class="p-2 bg-white rounded-lg shadow-md hover:bg-gray-100 transition"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 4v16m8-8H4"
          />
        </svg>
      </button>
      <button
        @click="zoomOut"
        class="p-2 bg-white rounded-lg shadow-md hover:bg-gray-100 transition"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M20 12H4"
          />
        </svg>
      </button>
    </div>

    <!-- Информация о координатах -->
    <!-- <div
      class="absolute bottom-4 left-4 p-3 bg-white rounded-lg shadow-md text-sm"
    >
      Координаты: {{ coordinates.lat.toFixed(4) }}, {{ coordinates.lng.toFixed(4) }}
    </div> -->
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Map from 'ol/Map';
import View from 'ol/View';
import TileLayer from 'ol/layer/Tile';
import OSM from 'ol/source/OSM';
import { fromLonLat, toLonLat } from 'ol/proj'; // Импортируем toLonLat
import { defaults as defaultControls } from 'ol/control';

// Состояние для координат
const coordinates = ref({ lat: 0, lng: 0 });

// Создание карты
let map;

onMounted(() => {
  map = new Map({
    target: 'map',
    layers: [
      new TileLayer({
        source: new OSM(),
      }),
    ],
    view: new View({
      center: fromLonLat([37.6176, 55.7558]), // Центр карты (долгота, широта)
      zoom: 10, // Начальный уровень масштабирования
    }),
    controls: defaultControls({
      zoom: false, // Отключаем стандартные кнопки увеличения/уменьшения
      rotate: false, // Отключаем стандартную кнопку поворота
      attribution: false, // Отключаем стандартную атрибуцию
    }),
  });

  // Обновление координат при движении карты
  map.on('pointermove', (event) => {
    const coords = map.getEventCoordinate(event.originalEvent);
    const lonLat = toLonLat(coords); // Преобразуем координаты в долготу/широту
    coordinates.value = { lat: lonLat[1], lng: lonLat[0] };
  });
});

// Увеличение масштаба
const zoomIn = () => {
  const view = map.getView();
  view.setZoom(view.getZoom() + 1);
};

// Уменьшение масштаба
const zoomOut = () => {
  const view = map.getView();
  view.setZoom(view.getZoom() - 1);
};
</script>

<style scoped>
#map {
  width: 100%;
  height: 100%;
}
</style>