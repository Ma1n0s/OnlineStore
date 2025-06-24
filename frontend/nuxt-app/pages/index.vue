<script setup lang="ts">
import BigSwiper from '~/components/Swiper/BigSwiper.vue'
import CategoryList from '~/components/CategoryItems/CategoryList/CategoryList.vue'
import WrapperHeader from '~/components/Wrapper/WrapperHeader.vue'
import Advantages from '~/components/Advantages/Advantages.vue'

const {
  public: { backendUrl },
} = useRuntimeConfig()

useHead({
  title: `Абсолют техно`,
  meta: [
    {
      name: 'description',
      content: `Инструменты и оборудование для строительства и ремота`,
    },
  ],
})

const { data: category } = await useAsyncData(`category-list`, () =>
  $fetch(`${backendUrl}/api/categories`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
    },
  })
)

const { data: slides } = await useAsyncData(`slides`, () =>
  $fetch(`${backendUrl}/api/swiper`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
    },
  })
)

console.log(slides.value.data, 'slides')

console.log(category.value, 'category')
</script>

<template>
  <div class="bg-white w-full ~text-sm/xs ~pb-4/8">
    <div class="max-w-screen-2xl mx-auto px-1 sm:px-2 lg:px-8">
      <div>
        <BigSwiper :slides="slides.data" />
      </div>

      <div class="p-2 lg:p-0">
        <WrapperHeader title="Категории товаров">
          <CategoryList :categories="category" />
        </WrapperHeader>
      </div>

      <div class="p-2 lg:p-0">
        <WrapperHeader title="Преимущества" class="text-center text-3xl mt-4">
          <Advantages />
        </WrapperHeader>
      </div>
    </div>
  </div>
</template>
