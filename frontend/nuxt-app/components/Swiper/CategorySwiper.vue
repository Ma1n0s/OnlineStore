<template>
  <ClientOnly>
    <swiper-container
      ref="containerRef"
      :navigation="true"
      :loop="true"
      :autoplay-delay="5000"
      slides-per-view="auto"
      :space-between="30"
      class="w-full h-[300px]"
    >
      <swiper-slide
        v-for="(category, idx) in categories"
        :key="idx"
        class="bg-white flex flex-col w-48 h-64 shadow-xl rounded-3xl overflow-hidden px-2 lg:px-0"
      >
        <NuxtLink :to="category.path" class="bg-white flex flex-col w-48 h-64 overflow-hidden">
          <div
            class="box-content h-14 p-1 pb-4 text-lg text-dark font-medium text-center flex items-center justify-center"
          >
            {{ category.name }}
          </div>
          <div class="flex-1 relative">
            <NuxtImg
              :alt="category.name"
              :src="category.src || 'no-photo.webp'"
              class="absolute h-full w-full object-cover"
              format="webp"
            />
          </div>
        </NuxtLink>
      </swiper-slide>
    </swiper-container>
  </ClientOnly>
</template>

<script setup lang="ts">
const containerRef = ref(null)
const categories = ref([
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

<style scoped>
@import 'swiper/css';
@import 'swiper/css/navigation';
@import 'swiper/css/effect-creative';

swiper-container {
  --swiper-navigation-color: rgb(38, 56, 58);
  --swiper-pagination-color: black;
}
</style>
