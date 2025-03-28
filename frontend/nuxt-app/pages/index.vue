<script setup lang="ts">
import BrandSwiper from "~/components/Swiper/BrandSwiper.vue";
import CategorySwiper from "~/components/Swiper/CategorySwiper.vue";
import ProductSwiper from "~/components/Swiper/ProductSwiper/ProductSwiper.vue";
</script>
<template>
  <div class="bg-white w-full ~text-sm/xs ~p-4/8 flex flex-col items-center">
    <div class="w-full xl:max-w-7xl">
      <div class="mb-16">
        <CategorySwiper />
      </div>
      <div class="mb-8">
        <ProductSwiper />
      </div>
      <BrandSwiper />
    </div>
  </div>
</template>
