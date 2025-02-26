<script setup>
import { ref } from 'vue';

const currentIndex = ref(0);
const items = ref([
  { img: 'image1.jpg', title: 'Инструмент' },
  { img: 'image2.jpg', title: 'Электрика' },
  { img: 'image3.jpg', title: 'Крепеж и фурнитура' },
  { img: 'image4.jpg', title: 'Силовая техника' },
  { img: 'image5.jpg', title: 'Ручной инструмент' },
  { img: 'image6.jpg', title: 'Станки' },
]);

const next = () => {
  currentIndex.value = (currentIndex.value + 1) % items.value.length;
};

const prev = () => {
  currentIndex.value = (currentIndex.value - 1 + items.value.length) % items.value.length;
};
</script>
<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <div class="relative overflow-hidden">
      <div class="flex transition-transform duration-500" :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
        <div v-for="(item, index) in items" :key="index" class="bg-white rounded-lg shadow-lg max-w-xs flex-shrink-0">
          <img :src="item.img" alt="Category" class="w-full h-40 object-cover rounded-t-lg">
          <h2 class="text-center mt-2 text-lg font-semibold">{{ item.title }}</h2>
        </div>
      </div>
      <button @click="prev" class="absolute left-0 top-1/2 transform -translate-y-1/2  bg-gray-300 p-2 rounded-full">←</button>
      <button @click="next" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-300 p-2 rounded-full">→</button>
    </div>
    
  </div>
</template>



