<script setup lang="ts">
// import BrandSwiper from '~/components/Swiper/BrandSwiper.vue'
// import CategorySwiper from '~/components/Swiper/CategorySwiper.vue'
// import ProductSwiper from '~/components/Swiper/ProductSwiper/ProductSwiper.vue'
import BigSwiper from '~/components/Swiper/BigSwiper.vue'
import CategoryList from '~/components/CategoryItems/CategoryList/CategoryList.vue'
import WrapperHeader from '~/components/Wrapper/WrapperHeader.vue'
import Advantages from '~/components/Advantages/Advantages.vue'

const { data: category } = await useFetch(() => `http://127.0.0.1:8000/api/categories`)
</script>
<template>
  <div class="bg-white w-full ~text-sm/xs ~pb-4/8 flex flex-col items-center">
    <div class="w-full xl:container lg:px-8 py-4">
      <div class="mb-16">
        <BigSwiper />
      </div>

      <div class="mb-16 px-2 lg:px-0">
        <WrapperHeader title="Кататегории товаров">
          <CategoryList :categories="category" />
        </WrapperHeader>
      </div>

      <div class="mb-16 px-2 lg:px-0">
        <WrapperHeader title="Преимущества" class="text-center text-3xl">
          <Advantages />
        </WrapperHeader>
      </div>
      <!-- <div class="mb-16">
        <WrapperHeader title="Кататегории товаров" class="px-2">
          <CategorySwiper />
        </WrapperHeader>
      </div> -->
      <!-- <div class="mb-8">
        <WrapperHeader title="Акции" class="px-2 lg:px-0">
          <ProductSwiper />
        </WrapperHeader>
      </div> -->
      <!-- <BrandSwiper /> -->
    </div>
  </div>
</template>
