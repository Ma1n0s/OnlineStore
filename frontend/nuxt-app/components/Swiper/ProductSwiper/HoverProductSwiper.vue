<template>
  <swiper-container :loop="false" :space-between="0" :slides-per-view="1" :pagination="true" class="w-full h-full">
    <swiper-slide
      v-for="(slide, idx) in slides"
      :key="idx"
      class="bg-white flex items-center justify-center text-2xl font-bold w-full h-full lg:px-0"
    >
      <NuxtImg
        :alt="`swiperslide produtc - ${idx}`"
        :src="slide.url"
        width="300"
        height="300"
        format="webp"
        class="w-full h-full object-fit"
      />
    </swiper-slide>
  </swiper-container>
</template>

<script setup lang="ts">
const { slides } = defineProps<{
  slides: {
    url: string
    position: string
    alt: string
    id: string
  }[]
}>()
</script>
