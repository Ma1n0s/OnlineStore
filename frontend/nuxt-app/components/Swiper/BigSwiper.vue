<template>
  <ClientOnly>
    <swiper-container
      :navigation="true"
      :loop="true"
      :autoplay-delay="5000"
      :space-between="30"
      slides-per-view="auto"
      class="w-full h-72 rounded-3xl"
    >
      <swiper-slide
        v-for="(slide, idx) in slides"
        :key="idx"
        class="bg-white flex items-center justify-center text-2xl font-bold w-full h-full rounded-3xl px-2 lg:px-0"
      >
        <NuxtImg :alt="slide.brand" :src="slide.src" format="webp" class="w-full h-full object-cover rounded-3xl" />
      </swiper-slide>
    </swiper-container>
  </ClientOnly>
</template>

<script setup lang="ts">
const slides = ref([
  {
    brand: 'Зубр',
    src: 'https://cdn.vseinstrumenti.ru/res/content/page_templates/e37a4e459931bb202ac81144bf28a0a5.jpeg',
  },
  {
    brand: 'STIHL',
    src: 'https://cdn.vseinstrumenti.ru/res/content/page_templates/8cda9503758e95e5abf3820ae1278488.jpeg',
  },
  {
    brand: 'Сибур',
    src: 'https://cdn.vseinstrumenti.ru/res/content/page_templates/5db4e95cb28531ba151e5df026156bcd.jpeg',
  },
  {
    brand: 'Haier',
    src: 'https://cdn.vseinstrumenti.ru/res/content/page_templates/4ce23a87a706eaf19ce2b4d12471a56c.jpeg',
  },
])
</script>

<style scoped>
@import 'swiper/css';
@import 'swiper/css/navigation';
@import 'swiper/css/effect-creative';

swiper-container {
  --swiper-navigation-color: #d10026;
}
</style>
