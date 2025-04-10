<template>
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
    <NuxtLink
      v-for="(category, index) in categories"
      :to="category.path"
      :key="index + category.name"
      class="h-fit sm:h-64 shadow-xl rounded-3xl overflow-hidden cursor-pointer"
    >
      <div class="relative h-32 sm:h-48 w-full">
        <NuxtImg :alt="category.name" :src="category.src" class="absolute h-full w-full object-cover" format="webp" />
      </div>
      <div
        class="box-content h-14 p-1 pb-4 ~text-sm/xs md:text-lg text-dark font-medium text-center flex items-center justify-center"
      >
        {{ category.name }}
      </div>
    </NuxtLink>
  </div>
</template>

<script lang="ts" setup>
const categories = ref([
  { name: 'Инструменты', src: '/Categories/Instruments.png', path: '/category/instrument' },
  {
    name: 'Электрика',
    src: '/Categories/Wire.png',
    path: 'category',
  },
  {
    name: 'Крепеж и фурнитура',
    src: '/Categories/Dowel.png',
    path: 'category/dowel',
  },
  { name: 'Станки', src: '/Categories/Schlefmachine.png', path: '/category/schlefmachine' },
  {
    name: 'Строительный материалы',
    src: '/Categories/Board.png',
    path: '/category/board',
  },
  {
    name: 'Сантехника',
    src: '/Categories/Faucet.png',
    path: '/category/faucet',
  },
  {
    name: 'Строительное оборудование',
    src: '/Categories/ConcreteMixer.png',
    path: '/category/concrete-mixer',
  },
  { name: 'Инструменты', src: '/Categories/Instruments.png', path: '/category/instrument' },
  {
    name: 'Электрика',
    src: '/Categories/Wire.png',
    path: '/category/wire',
  },
  {
    name: 'Крепеж и фурнитура',
    src: '/Categories/Dowel.png',
    path: '/category/dowel',
  },
  { name: 'Станки', src: '/Categories/Schlefmachine.png', path: '/category/schlefmachine' },
  {
    name: 'Строительный материалы',
    src: '/Categories/Board.png',
    path: '/category/board',
  },
  {
    name: 'Сантехника',
    src: '/Categories/Faucet.png',
    path: '/category/faucet',
  },
  {
    name: 'Строительное оборудование',
    src: '/Categories/ConcreteMixer.png',
    path: '/category/concrete-mixer',
  },
  { name: 'Инструменты', src: '/Categories/Instruments.png', path: '/category/instrument' },
  {
    name: 'Электрика',
    src: '/Categories/Wire.png',
    path: '/category/wire',
  },
  {
    name: 'Крепеж и фурнитура',
    src: '/Categories/Dowel.png',
    path: '/category/dowel',
  },
  { name: 'Станки', src: '/Categories/Schlefmachine.png', path: '/category/schlefmachine' },
  {
    name: 'Строительный материалы',
    src: '/Categories/Board.png',
    path: '/category/board',
  },
  {
    name: 'Сантехника',
    src: '/Categories/Faucet.png',
    path: '/category/faucet',
  },
  {
    name: 'Строительное оборудование',
    src: '/Categories/ConcreteMixer.png',
    path: '/category/concrete-mixer',
  },
])
</script>
