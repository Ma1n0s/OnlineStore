<template>
  <div class="w-full md:w-2/5">
    <div class="bg-white rounded-lg p-4 relative">
      <ClientOnly>
        <div class="flex flex-row-reverse gap-4">
          <!-- Основной слайдер -->
          <div class="w-full lg:w-[calc(100%-80px)]">
            <swiper-container
              ref="mainSwiper"
              class="w-full h-80 md:h-96 mb-4"
              :space-between="10"
              :navigation="true"
              :loop="false"
              @slide-change="onMainSlideChange"
            >
              <swiper-slide v-for="(image, index) in product.images" :key="'main-' + index">
                <div class="w-full h-full flex items-center justify-center bg-gray-50">
                  <NuxtImg
                    :src="image.url"
                    :alt="product.alt || product.name"
                    format="webp"
                    class="max-w-full max-h-full object-contain"
                    loading="lazy"
                    sizes="sm:100vw md:50vw lg:400px"
                    @click="openFullscreenImage(index)"
                  />
                </div>
              </swiper-slide>
            </swiper-container>
          </div>

          <!-- Вертикальный слайдер миниатюр -->
          <div class="w-16 hidden lg:flex flex-col">
            <button
              v-if="product.images.length > 4"
              @click="scrollThumbsUp"
              class="text-gray-800 hover:text-red-500 transition-colors mb-2"
            >
              <Icon name="material-symbols:keyboard-arrow-up" class="w-5 h-5" />
            </button>

            <swiper-container
              ref="thumbsSwiper"
              class="thumbs-swiper h-80"
              :space-between="8"
              :slides-per-view="4"
              :direction="'vertical'"
              :free-mode="true"
              :watch-slides-progress="true"
              :mousewheel="true"
            >
              <swiper-slide
                v-for="(image, index) in product.images"
                :key="'thumb-' + index"
                @click="slideTo(index)"
                :class="{ 'border-red-500': activeIndex === index, 'border-transparent': activeIndex !== index }"
              >
                <NuxtImg
                  :src="image.url"
                  :alt="'Изображение ' + (index + 1)"
                  format="webp"
                  class="w-full h-full object-cover rounded cursor-pointer border border-gray-200"
                  loading="lazy"
                  width="80"
                  height="80"
                />
              </swiper-slide>
            </swiper-container>

            <button
              v-if="product.images.length > 4"
              @click="scrollThumbsDown"
              class="text-gray-800 hover:text-red-500 transition-colors mt-2"
            >
              <Icon name="material-symbols:keyboard-arrow-down" class="w-5 h-5" />
            </button>
          </div>
        </div>

        <!-- Горизонтальный слайдер миниатюр для мобильных -->
        <div class="lg:hidden mt-4">
          <swiper-container
            ref="mobileThumbsSwiper"
            class="mobile-thumbs-swiper"
            :space-between="8"
            :slides-per-view="4"
            :free-mode="true"
            :watch-slides-progress="true"
          >
            <swiper-slide
              v-for="(image, index) in product.images"
              :key="'mobile-thumb-' + index"
              @click="slideTo(index)"
              :class="{ 'border-red-500': activeIndex === index, 'border-transparent': activeIndex !== index }"
            >
              <NuxtImg
                :src="image.url"
                :alt="'Изображение ' + (index + 1)"
                format="webp"
                class="w-full h-16 object-cover rounded cursor-pointer border border-gray-200"
                loading="lazy"
                width="80"
                height="64"
              />
            </swiper-slide>
          </swiper-container>
        </div>
      </ClientOnly>
    </div>

    <!-- Fullscreen режим -->
    <div v-if="isFullscreenOpen" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-white p-4">
      <button
        class="absolute top-4 right-4 text-gray-800 text-2xl z-50 hover:text-red-500 transition-colors"
        @click="closeFullscreenImage"
      >
        &times;
      </button>

      <div class="relative w-full h-4/5 flex items-center justify-center mb-4">
        <div class="max-w-full max-h-full flex items-center justify-center">
          <NuxtImg
            :src="product.images[fullscreenIndex].url"
            :alt="product.name"
            format="webp"
            class="max-w-full max-h-full object-contain"
            loading="eager"
          />
        </div>

        <button
          v-if="product.images.length > 1"
          class="absolute left-4 top-1/2 transform -translate-y-1/2 text-red-500 text-4xl z-50 hover:text-red-700 transition-colors"
          @click="prevFullscreenImage"
        >
          <Icon name="material-symbols:arrow-left-alt" />
        </button>

        <button
          v-if="product.images.length > 1"
          class="absolute right-4 top-1/2 transform -translate-y-1/2 text-red-500 text-4xl z-50 hover:text-red-700 transition-colors"
          @click="nextFullscreenImage"
        >
          <Icon name="material-symbols:arrow-right-alt" />
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
              format="webp"
              class="w-full h-full object-cover rounded cursor-pointer border border-gray-200"
              loading="lazy"
              width="80"
              height="80"
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
const mobileThumbsSwiper = ref<any>(null)
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
    activeIndex.value = index
  }
}

const scrollThumbsUp = () => {
  if (thumbsSwiper.value && thumbsSwiper.value.swiper) {
    thumbsSwiper.value.swiper.slidePrev()
  }
}

const scrollThumbsDown = () => {
  if (thumbsSwiper.value && thumbsSwiper.value.swiper) {
    thumbsSwiper.value.swiper.slideNext()
  }
}

const openFullscreenImage = (index: number) => {
  fullscreenIndex.value = index
  isFullscreenOpen.value = true
  document.body.style.overflow = 'hidden'
  document.documentElement.style.overflow = 'hidden'

  nextTick(() => {
    if (fullscreenThumbsSwiper.value && fullscreenThumbsSwiper.value.swiper) {
      fullscreenThumbsSwiper.value.swiper.slideTo(index)
    }
  })
}

const closeFullscreenImage = () => {
  isFullscreenOpen.value = false
  document.body.style.overflow = ''
  document.documentElement.style.overflow = ''
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
  watch([mainSwiper, thumbsSwiper, mobileThumbsSwiper], ([main, thumbs, mobileThumbs]) => {
    if (main && thumbs) {
      main.swiper.controller.control = thumbs.swiper
      thumbs.swiper.controller.control = main.swiper
    }
    if (main && mobileThumbs) {
      main.swiper.controller.control = mobileThumbs.swiper
      mobileThumbs.swiper.controller.control = main.swiper
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

swiper-container {
  --swiper-navigation-color: #d10026;
  --swiper-navigation-size: 24px;
}

.thumbs-swiper,
.fullscreen-thumbs-swiper,
.mobile-thumbs-swiper {
  padding: 4px 0;
}

.thumbs-swiper swiper-slide,
.mobile-thumbs-swiper swiper-slide {
  opacity: 0.8;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  border-radius: 0.375rem;
}

.thumbs-swiper swiper-slide:hover,
.mobile-thumbs-swiper swiper-slide:hover {
  opacity: 1;
  transform: scale(1.03);
}

.thumbs-swiper swiper-slide.border-red-500,
.mobile-thumbs-swiper swiper-slide.border-red-500 {
  opacity: 1;
  border-color: #ef4444;
}

.thumbs-swiper swiper-slide.border-red-500 img,
.mobile-thumbs-swiper swiper-slide.border-red-500 img {
  filter: brightness(1.05);
}

.fullscreen-thumbs-swiper {
  --swiper-navigation-color: #d10026;
}

.active-thumb {
  border-color: #ef4444 !important;
  opacity: 1 !important;
}
</style>