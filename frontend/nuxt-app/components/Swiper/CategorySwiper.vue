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
        class="bg-white flex flex-col w-48 h-64 shadow-xl rounded-3xl overflow-hidden"
      >
        <div class="box-content h-14 p-1 pb-4 text-lg text-dark font-medium">{{ category.name }}</div>
        <div class="flex-1 relative">
          <!-- Контейнер для изображения -->
          <NuxtImg :alt="category.name" :src="category.src" class="absolute h-full w-full object-cover" format="webp" />
        </div>
      </swiper-slide>
    </swiper-container>
  </ClientOnly>
</template>

<script setup lang="ts">
const containerRef = ref(null);
const categories = ref([
  { name: "Инструменты", src: "/Categories/Instruments.png" },
  {
    name: "Электрика",
    src: "/Categories/Wire.png",
  },
  {
    name: "Крепеж и фурнитура",
    src: "/Categories/Dowel.png",
  },
  { name: "Станки", src: "/Categories/Schlefmachine.png" },
  {
    name: "Строительный материалы",
    src: "/Categories/Board.png",
  },
  {
    name: "Сантехника",
    src: "/Categories/Faucet.png",
  },
  {
    name: "Строительное оборудование",
    src: "/Categories/ConcreteMixer.png",
  },
]);
</script>

<style scoped>
@import "swiper/css";
@import "swiper/css/navigation";
@import "swiper/css/effect-creative";

swiper-container {
  --swiper-navigation-color: rgb(38, 56, 58);
  --swiper-pagination-color: black;
}
</style>
