<script setup lang="ts">
import BigSwiper from '~/components/Swiper/BigSwiper.vue'
import CategoryList from '~/components/CategoryItems/CategoryList/CategoryList.vue'
import WrapperHeader from '~/components/Wrapper/WrapperHeader.vue'
import Advantages from '~/components/Advantages/Advantages.vue'

const {
  public: { backendUrl },
} = useRuntimeConfig()

const { data: category } = await useAsyncData(`category-list`, () => $fetch(`${backendUrl}/api/categories`), {
  revalidate: 3600,
})

// const { data: slides } = await useAsyncData(`slides`, () => $fetch(`${backendUrl}/api/swiper`), {
//   revalidate: 3600,
// })

const slidesTime = ref([
  {
    brand: 'Зубр',
    src: 'https://cdn.vseinstrumenti.ru/res/content/page_templates/e37a4e459931bb202ac81144bf28a0a5.jpeg',
    url: '/news/1',
  },
  {
    brand: 'STIHL',
    src: 'https://cdn.vseinstrumenti.ru/res/content/page_templates/8cda9503758e95e5abf3820ae1278488.jpeg',
    url: '/news/2',
  },
  {
    brand: 'Сибур',
    src: 'https://cdn.vseinstrumenti.ru/res/content/page_templates/5db4e95cb28531ba151e5df026156bcd.jpeg',
    url: '/news/3',
  },
  {
    brand: 'Haier',
    src: 'https://cdn.vseinstrumenti.ru/res/content/page_templates/4ce23a87a706eaf19ce2b4d12471a56c.jpeg',
    url: '/news/4',
  },
])

console.log(category.value, 'category')
</script>

<template>
  <div class="bg-white w-full ~text-sm/xs ~pb-4/8">
    <div class="mx-auto w-full max-w-screen-2xl px-8 space-y-8 py-8">
      <div>
        <BigSwiper :slides="slidesTime" />
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
