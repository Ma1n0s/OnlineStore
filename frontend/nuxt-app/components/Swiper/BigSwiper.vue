<template>
  <ClientOnly>
    <div class="button-atrr">
      <swiper-container
        :loop="true"
        :autoplay-delay="5000"
        :space-between="30"
        slides-per-view="auto"
        class="w-full h-72"
        ref="swiperRef"
      >
        <swiper-slide
          v-for="(slide, idx) in slides"
          :key="idx"
          class="bg-white flex items-center justify-center text-2xl font-bold w-full h-full lg:px-0"
        >
          <NuxtImg :alt="slide.brand" :src="slide.src" format="webp" class="w-full h-full object-cover" />
        </swiper-slide>
      </swiper-container>

      <div class="wiper-button-prev" @click="slidePrev">
        <Icon name="material-symbols:navigate-before" class="w-8 h-8" />
      </div>
      <div class="wiper-button-next" @click="slideNext">
        <Icon name="material-symbols:navigate-next" class="w-8 h-8" />
      </div>
    </div>
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

const swiperRef = ref(null)

const slideNext = () => {
  if (swiperRef.value) {
    swiperRef.value.swiper.slideNext()
  }
}

const slidePrev = () => {
  if (swiperRef.value) {
    swiperRef.value.swiper.slidePrev()
  }
}
</script>

<style scoped>
@import 'swiper/css';
@import 'swiper/css/effect-creative';

.button-atrr {
  position: relative;
}

.wiper-button-prev,
.wiper-button-next {
  position: absolute;
  top: 45%;
  /* transform: translateY(-50%); */
  width: 40px;
  height: 40px;
  background-color: rgba(255, 255, 255, 0.8);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  color: #d10026;
  font-weight: bold;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.wiper-button-prev {
  left: 10px;
}

.wiper-button-next {
  right: 10px;
}

.swiper-button-prev::after {
  background-repeat: no-repeat;
  width: 100px;
  height: 100px;
  color: #ffffff00;
}

.swiper-button-prev.swiper-button-disabled::after {
  background-repeat: no-repeat !important;
  width: 100px !important;
  height: 100px !important;
  color: #ffffff00 !important;
}

.swiper-button-next {
  background-repeat: no-repeat;
  background-size: 100% auto;
  background-position: center;
}
</style>
