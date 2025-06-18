<script setup>
import ProductCard from '~/components/ProductCard.vue';
useHead({
  title: 'Продуктовая карточка',
  meta: [
    {
      name: 'description',
      content: 'Пример продуктовой карточки с Nuxt 3 и Tailwind CSS',
    },
  ],
})
</script>

<template>
  <div>
    <ProductCard />
  </div>
</template>