<template>
  <div class="w-full md:w-2/5">
    <div class="bg-white rounded-lg shadow-md p-4 relative">
      <ClientOnly>
        <swiper-container
          ref="mainSwiper"
          class="w-full h-80 mb-4"
          :space-between="10"
          :navigation="true"
          :loop="false"
          @slide-change="onMainSlideChange"
        >
          <swiper-slide v-for="(image, index) in product.images" :key="'main-' + index">
            <NuxtImg
              :src="image.url"
              :alt="product.alt"
              class="w-full h-full object-contain"
              @click="openFullscreenImage(index)"
            />
          </swiper-slide>
        </swiper-container>

        <swiper-container
          ref="thumbsSwiper"
          class="thumbs-swiper h-16"
          :space-between="8"
          :slides-per-view="4"
          :free-mode="true"
          :watch-slides-progress="true"
        >
          <swiper-slide
            v-for="(image, index) in product.images"
            :key="'thumb-' + index"
            @click="slideTo(index)"
            :class="{ 'active-thumb': activeIndex === index }"
          >
            <NuxtImg
              :src="image.url"
              :alt="'Изображение ' + (index + 1)"
              class="w-full h-full object-cover rounded cursor-pointer border"
            />
          </swiper-slide>
        </swiper-container>
      </ClientOnly>
    </div>

    <div v-if="isFullscreenOpen" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-white p-4">
      <button
        class="absolute top-4 right-4 text-gray-800 text-2xl z-50 hover:text-red-500 transition-colors"
        @click="closeFullscreenImage"
      >
        &times;
      </button>

      <div class="relative w-full h-4/5 flex items-center justify-center mb-4">
        <NuxtImg
          :src="product.images[fullscreenIndex].url"
          :alt="product.name"
          class="max-w-full max-h-full object-contain"
        />

        <button
          v-if="product.images.length > 1"
          class="absolute left-4 top-1/2 transform -translate-y-1/2 text-red-500 text-4xl z-50 hover:text-red-700 transition-colors"
          @click="prevFullscreenImage"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-red-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <button
          v-if="product.images.length > 1"
          class="absolute right-4 top-1/2 transform -translate-y-1/2 text-red-500 text-4xl z-50 hover:text-red-700 transition-colors"
          @click="nextFullscreenImage"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-red-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      <div class="w-full max-w-2xl mx-auto">
        <swiper-container
          ref="fullscreenThumbsSwiper"
          class="fullscreen-thumbs-swiper h-20"
          :space-between="8"
          :slides-per-view="product.images.length > 4 ? 4 : product.images.length"
          :free-mode="true"
          :watch-slides-progress="true"
        >
          <swiper-slide
            v-for="(image, index) in product.images"
            :key="'fullscreen-thumb-' + index"
            @click="goToFullscreenImage(index)"
            :class="{ 'active-thumb': fullscreenIndex === index }"
          >
            <NuxtImg
              :src="image.url"
              :alt="'Изображение ' + (index + 1)"
              class="w-full h-full object-cover rounded cursor-pointer border"
            />
          </swiper-slide>
        </swiper-container>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { register } from 'swiper/element/bundle'
import type { Product } from '~/types/product.types'

const { product } = defineProps<{
  product: Product
}>()

register()

const mainSwiper = ref<any>(null)
const thumbsSwiper = ref<any>(null)
const fullscreenThumbsSwiper = ref<any>(null)
const activeIndex = ref(0)
const isFullscreenOpen = ref(false)
const fullscreenIndex = ref(0)

const onMainSlideChange = (e: any) => {
  activeIndex.value = e.detail[0].realIndex
}

const slideTo = (index: number) => {
  if (mainSwiper.value && mainSwiper.value.swiper) {
    mainSwiper.value.swiper.slideTo(index)
  }
}

const openFullscreenImage = (index: number) => {
  fullscreenIndex.value = index
  isFullscreenOpen.value = true
  document.body.style.overflow = 'hidden'

  nextTick(() => {
    if (fullscreenThumbsSwiper.value && fullscreenThumbsSwiper.value.swiper) {
      fullscreenThumbsSwiper.value.swiper.slideTo(index)
    }
  })
}

const closeFullscreenImage = () => {
  isFullscreenOpen.value = false
  document.body.style.overflow = ''
}

const prevFullscreenImage = () => {
  fullscreenIndex.value = (fullscreenIndex.value - 1 + product.images.length) % product.images.length
  syncFullscreenThumbs()
}

const nextFullscreenImage = () => {
  fullscreenIndex.value = (fullscreenIndex.value + 1) % product.images.length
  syncFullscreenThumbs()
}

const goToFullscreenImage = (index: number) => {
  fullscreenIndex.value = index
  syncFullscreenThumbs()
}

const syncFullscreenThumbs = () => {
  if (fullscreenThumbsSwiper.value && fullscreenThumbsSwiper.value.swiper) {
    fullscreenThumbsSwiper.value.swiper.slideTo(fullscreenIndex.value)
  }
}

const handleKeyDown = (e: KeyboardEvent) => {
  if (!isFullscreenOpen.value) return

  if (e.key === 'Escape') {
    closeFullscreenImage()
  } else if (e.key === 'ArrowLeft') {
    prevFullscreenImage()
  } else if (e.key === 'ArrowRight') {
    nextFullscreenImage()
  }
}

onMounted(() => {
  watch([mainSwiper, thumbsSwiper], ([main, thumbs]) => {
    if (main && thumbs) {
      main.swiper.controller.control = thumbs.swiper
      thumbs.swiper.controller.control = main.swiper
    }
  })

  window.addEventListener('keydown', handleKeyDown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown)
})
</script>

<style scoped>
@import 'swiper/css';
@import 'swiper/css/navigation';
@import 'swiper/css/thumbs';

swiper-container {
  --swiper-navigation-color: #d10026;
  --swiper-navigation-size: 24px;
}

.thumbs-swiper,
.fullscreen-thumbs-swiper {
  padding: 4px 0;
}

.thumbs-swiper swiper-slide,
.fullscreen-thumbs-swiper swiper-slide {
  opacity: 0.6;
  transition: opacity 0.3s;
  border: 2px solid transparent;
}

.thumbs-swiper swiper-slide:hover,
.fullscreen-thumbs-swiper swiper-slide:hover {
  opacity: 1;
}

.thumbs-swiper .active-thumb,
.fullscreen-thumbs-swiper .active-thumb {
  opacity: 1;
  border-color: #d10026 !important;
}

.cursor-zoom-in {
  cursor: zoom-in;
}

.fullscreen-thumbs-swiper {
  --swiper-navigation-color: #d10026;
}
</style>
