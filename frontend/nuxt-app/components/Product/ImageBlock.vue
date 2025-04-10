<template>
  <div class="w-full md:w-2/5">
    <div class="bg-white rounded-lg shadow-md p-4">
      <ClientOnly>
        <!-- Main swiper for large image -->
        <swiper-container
          ref="mainSwiper"
          class="w-full h-80 mb-4"
          :space-between="10"
          :navigation="true"
          :loop="false"
          @slide-change="onMainSlideChange"
        >
          <swiper-slide v-for="(image, index) in product.images" :key="'main-' + index">
            <NuxtImg :src="image.src" :alt="product.name" class="w-full h-full object-contain" />
          </swiper-slide>
        </swiper-container>

        <!-- Thumbnail swiper -->
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
              :src="image.src"
              :alt="'Изображение ' + (index + 1)"
              class="w-full h-full object-cover rounded cursor-pointer border"
            />
          </swiper-slide>
        </swiper-container>
      </ClientOnly>
    </div>
  </div>
</template>

<script setup lang="ts">
import { productData } from '~/shared/productData'
import type { Product } from '~/types/product.types'
import { register } from 'swiper/element/bundle'

// Register Swiper custom elements
register()

const product = reactive<Product>(productData)
const mainSwiper = ref<any>(null)
const thumbsSwiper = ref<any>(null)
const activeIndex = ref(0)

const onMainSlideChange = (e: any) => {
  activeIndex.value = e.detail[0].realIndex
}

const slideTo = (index: number) => {
  if (mainSwiper.value && mainSwiper.value.swiper) {
    mainSwiper.value.swiper.slideTo(index)
  }
}

// Initialize swipers after mount
onMounted(() => {
  watch([mainSwiper, thumbsSwiper], ([main, thumbs]) => {
    if (main && thumbs) {
      main.swiper.controller.control = thumbs.swiper
      thumbs.swiper.controller.control = main.swiper
    }
  })
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

.thumbs-swiper {
  padding: 4px 0;
}

.thumbs-swiper swiper-slide {
  opacity: 0.6;
  transition: opacity 0.3s;
  border: 2px solid transparent;
}

.thumbs-swiper swiper-slide:hover {
  opacity: 1;
}

.thumbs-swiper .active-thumb {
  opacity: 1;
  border-color: #d10026 !important;
}
</style>
