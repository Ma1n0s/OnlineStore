<script setup lang="ts">
import BigSwiper from '~/components/Swiper/BigSwiper.vue'
import CategoryList from '~/components/CategoryItems/CategoryList/CategoryList.vue'
import WrapperHeader from '~/components/Wrapper/WrapperHeader.vue'
import Advantages from '~/components/Advantages/Advantages.vue'

const { data: category } = await useFetch(() => `http://127.0.0.1:8000/api/categories`)
</script>

<template>
  <div class="bg-white w-full ~text-sm/xs ~pb-4/8">
    <div class="mx-auto w-full max-w-screen-2xl px-8 space-y-16 py-8">
      <div>
        <BigSwiper />
      </div>

      <div>
        <WrapperHeader title="Категории товаров">
          <CategoryList :categories="category" />
        </WrapperHeader>
      </div>

      <div>
        <WrapperHeader title="Преимущества" class="text-center text-3xl">
          <Advantages />
        </WrapperHeader>
      </div>
    </div>
  </div>
</template>
