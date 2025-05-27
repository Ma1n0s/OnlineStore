<script setup lang="ts">
import BigSwiper from '~/components/Swiper/BigSwiper.vue'
import CategoryList from '~/components/CategoryItems/CategoryList/CategoryList.vue'
import WrapperHeader from '~/components/Wrapper/WrapperHeader.vue'
import Advantages from '~/components/Advantages/Advantages.vue'

const {
  public: { backendUrl },
} = useRuntimeConfig()

const { data: category } = await useAsyncData(`category-list`, () => $fetch(`${backendUrl}/api/categories`))

const { data: slides } = await useAsyncData(`slides`, () => $fetch(`${backendUrl}/api/swiper`))

console.log(slides.value.data, 'slides')

// const slidesTime = ref([
//   {
//     brand: 'Зубр',
//     image: 'https://cdn.vseinstrumenti.ru/res/content/page_templates/e37a4e459931bb202ac81144bf28a0a5.jpeg',
//     link: '/news/1',
//   },
//   {
//     brand: 'STIHL',
//     image: 'https://cdn.vseinstrumenti.ru/res/content/page_templates/8cda9503758e95e5abf3820ae1278488.jpeg',
//     link: '/news/2',
//   },
//   {
//     brand: 'Сибур',
//     image: 'https://cdn.vseinstrumenti.ru/res/content/page_templates/5db4e95cb28531ba151e5df026156bcd.jpeg',
//     link: '/news/3',
//   },
//   {
//     brand: 'Haier',
//     image: 'https://cdn.vseinstrumenti.ru/res/content/page_templates/4ce23a87a706eaf19ce2b4d12471a56c.jpeg',
//     link: '/news/4',
//   },
// ])

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
        <WrapperHeader title="Преимущества" class="text-center text-3xl">
          <Advantages />
        </WrapperHeader>
      </div>
    </div>
  </div>
</template>
