<template>
  <ClientOnly>
    <swiper-container :loop="false" :space-between="0" :slides-per-view="1" :pagination="true" class="w-full h-full">
      <swiper-slide v-for="(slide, idx) in slides" :key="idx" class="w-full h-full">
        <div class="w-full h-full flex items-center justify-center bg-gray-100">
          <NuxtImg
            :alt="slide.alt || `Product image ${idx}`"
            :src="slide.url || 'no-photo.webp'"
            format="webp"
            :class="['max-w-full max-h-full', mode === 'grid' ? 'object-contain' : 'object-cover']"
            loading="lazy"
            sizes="sm:300px md:400px lg:500px"
          />
        </div>
      </swiper-slide>

      <swiper-slide v-if="!slides || !slides.length">
        <div>
          <NuxtImg
            :src="'no-photo.webp'"
            :alt="'No img'"
            format="webp"
            :class="['max-w-full max-h-full', mode === 'grid' ? 'object-contain' : 'object-cover']"
            loading="lazy"
          />
        </div>
      </swiper-slide>
    </swiper-container>
  </ClientOnly>
</template>

<script setup lang="ts">
const props = defineProps<{
  slides: {
    url: string
    position: string
    alt: string
    id: string
  }[]
  mode?: 'grid' | 'list'
}>()

const mode = props.mode || 'list'
</script>
