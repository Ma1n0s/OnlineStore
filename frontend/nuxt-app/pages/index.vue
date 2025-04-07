<script setup lang="ts">
import BrandSwiper from "~/components/Swiper/BrandSwiper.vue";
import CategorySwiper from "~/components/Swiper/CategorySwiper.vue";
import ProductSwiper from "~/components/Swiper/ProductSwiper/ProductSwiper.vue";
import BigSwiper from "~/components/Swiper/BigSwiper.vue";
import CategoryList from "~/components/CategoryList/CategoryList.vue";
</script>
<template>
  <div class="bg-white w-full ~text-sm/xs ~p-4/8 flex flex-col items-center">
    <div class="w-full xl:max-w-7xl">
      <div class="mb-16">
        <BigSwiper />
      </div>

      <div class="mb-16">
        <WrapperHeader title="Кататегории товаров">
          <CategoryList />
        </WrapperHeader>
      </div>

      <div class="mb-16">
        <WrapperHeader title="Кататегории товаров">
          <CategorySwiper />
        </WrapperHeader>
      </div>
      <div class="mb-8">
        <WrapperHeader title="Акции">
          <ProductSwiper />
        </WrapperHeader>
      </div>
      <BrandSwiper />
    </div>
  </div>
</template>
