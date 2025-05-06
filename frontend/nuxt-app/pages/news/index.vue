// можно без обертки чтобы были элементы title картинка и текст
<script setup>
import ArticleCard from '~/components/News/ArticleCard.vue'

const articles = [
  // {
  //   id: 1,
  //   title: 'Создание странички',
  //   excerpt: 'Небольшой тестовый текст по проверка внешнего вида.',
  //   date: 'Апреля 10, 2025',
  //   image:
  //     'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
  // },
  // {
  //   id: 2,
  //   title: 'Создание странички',
  //   excerpt: 'Небольшой тестовый текст по проверка внешнего вида.',
  //   date: 'Апреля 10, 2025',
  //   image:
  //     'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
  // },
  // {
  //   id: 3,
  //   title: 'Создание странички',
  //   excerpt: 'Небольшой тестовый текст по проверка внешнего вида.',
  //   date: 'Апреля 10, 2025',
  //   image:
  //     'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
  // },
  // {
  //   id: 4,
  //   title: 'Создание странички',
  //   excerpt: 'Небольшой тестовый текст по проверка внешнего вида.',
  //   date: 'Апреля 10, 2025',
  //   image:
  //     'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
  // },
  // {
  //   id: 5,
  //   title: 'Создание странички',
  //   excerpt: 'Небольшой тестовый текст по проверка внешнего вида.',
  //   date: 'Апреля 10, 2025',
  //   image:
  //     'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
  // },
  // {
  //   id: 6,
  //   title: 'Создание странички',
  //   excerpt: 'Небольшой тестовый текст по проверка внешнего вида.',
  //   date: 'Апреля 10, 2025',
  //   image:
  //     'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
  // },
]
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <div class="mx-auto w-full max-w-screen-2xl px-8 space-y-8 py-8">
      <div class="grid grid-cols-1 mx-6 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <ArticleCard
          v-for="article in articles"
          :key="article.id"
          :article="article"
          class="hover:scale-105 transition-transform duration-200"
        />
      </div>
    </div>
  </div>
</template>
