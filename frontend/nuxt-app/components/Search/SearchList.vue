<template>
  <div class="box-border absolute mt-1 w-full bg-white shadow-2xl p-1 rounded-xl z-10">
    <div
      v-for="(item, index) in data.categories"
      :key="index"
      class="flex items-center p-1 py-1 cursor-pointer first:rounded-t-xl hover:bg-red-100"
    >
      <NuxtImg :src="item.src" width="40" height="40" />
      <div class="p-1 pl-2">
        <div class="">{{ item.subcategory }}</div>
        <div class="text-dark/50 text-sm">{{ item.category }}</div>
      </div>
    </div>
    <hr class="border-primary/50 my-1" />
    <div
      v-for="(item, index) in data.items"
      :key="index"
      class="flex items-center p-1 py-1 cursor-pointer last:rounded-b-xl hover:bg-red-100"
    >
      <NuxtImg :src="item.src" width="60" height="60" />
      <div>
        <div class="p-1 pl-2">
          {{ item.name }}
        </div>
        <div class="p-1 pl-2">{{ item.price }} руб.</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const data = ref({
  categories: [
    {
      subcategory: 'Шуруповерты',
      category: 'Инструменты',
      src: 'https://fi.makitamedia.com/images/3_Makita/301_machines/3011_a_GS1/30118_PNG_web/M8301D002_C2L0.png',
    },
    {
      subcategory: 'Шурупы',
      category: 'Крепеж',
      src: 'https://princefastener.com/wp-content/uploads/2022/03/figure-1.-screws-300x300.png',
    },
  ],
  items: [
    {
      name: 'Шуруповерт Makita',
      category: 'Инструменты',
      subcategory: 'Шуруповерты',
      src: 'https://fi.makitamedia.com/images/3_Makita/301_machines/3011_a_GS1/30118_PNG_web/M8301D002_C2L0.png',
      price: 10000,
    },
    {
      name: 'Шуруповерт Ingersoll Rand L5110-K1',
      category: 'Инструменты',
      subcategory: 'Шуруповерты',
      src: 'https://hotech-ms.com/wp-content/uploads/2020/11/W7150EU-WEB-01-300x300.png',
      price: 7000,
    },
  ],
})

const { target } = defineProps<{
  target: HTMLElement
}>()

const emit = defineEmits(['close'])

const showSearch = defineModel<boolean>('showSearch', { required: true })

onClickOutside(target, () => {
  showSearch.value = false
  emit('close')
})
</script>
