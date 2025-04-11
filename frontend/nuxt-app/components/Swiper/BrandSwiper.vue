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
        v-for="(slide, idx) in slides"
        :key="idx"
        class="bg-white flex items-center justify-center text-2xl font-bold w-[300px] h-full px-2 lg:px-0"
      >
        <NuxtImg :alt="slide.brand" :src="slide.src" format="webp" height="100%" width="100%" />
      </swiper-slide>
    </swiper-container>
  </ClientOnly>
</template>

<script setup lang="ts">
const containerRef = ref(null)
const slides = ref([
  { brand: 'Зубр', src: 'https://www.mitaro.ru/upload/iblock/a28/a2862f71e2516cb5559bdfde4d05b49e.png' },
  {
    brand: 'STIHL',
    src: 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/38/Stihl_Logo_WhiteOnOrange.svg/400px-Stihl_Logo_WhiteOnOrange.svg.png',
  },
  { brand: 'Сибур', src: 'https://otpugivateli.ru/1nmvYe/1kJmnZ/images/Novosti/logo_sibur.jpg' },
  { brand: 'Haier', src: 'https://files.sikayetvar.com/lg/cmp/15/15032.png' },
  {
    brand: 'Dewalt',
    src: 'https://m.media-amazon.com/images/S/aplus-media/sota/da47e228-6768-4ecb-b694-d1488ff237cc.__CR0,0,600,180_PT0_SX600_V1___.png',
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
