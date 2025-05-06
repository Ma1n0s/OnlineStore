<script setup>
const route = useRoute()
const { id } = route.params

const articles = [
  {
    id: 1,
    title: 'Создание странички',
    content: 'Полный текст статьи 1...',
    excerpt: 'Небольшой тестовый текст по проверка внешнего вида.',
    date: 'Апреля 10, 2025',
    image:
      'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
  },
  {
    id: 2,
    title: 'Создание странички',
    content: 'Полный текст статьи 2...',
    excerpt: 'Небольшой тестовый текст по проверка внешнего вида.',
    date: 'Апреля 10, 2025',
    image:
      'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
  },
  {
    id: 3,
    title: 'Создание странички',
    content: 'Полный текст статьи 3...',
    excerpt: 'Небольшой тестовый текст по проверка внешнего вида.',
    date: 'Апреля 10, 2025',
    image:
      'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
  },
  {
    id: 4,
    title: 'Создание странички',
    content: 'Полный текст статьи 4...',
    excerpt: 'Небольшой тестовый текст по проверка внешнего вида.',
    date: 'Апреля 10, 2025',
    image:
      'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
  },
  {
    id: 5,
    title: 'Создание странички',
    content: 'Полный текст статьи 5...',
    excerpt: 'Небольшой тестовый текст по проверка внешнего вида.',
    date: 'Апреля 10, 2025',
    image:
      'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
  },
  {
    id: 6,
    title: 'Создание странички',
    content: 'Полный текст статьи 6...',
    excerpt: 'Небольшой тестовый текст по проверка внешнего вида.',
    date: 'Апреля 10, 2025',
    image:
      'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
  },
]

const article = articles.find(article => article.id === Number(id))
</script>

<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="mx-auto w-full max-w-screen-2xl px-8">
      <article class="bg-white rounded-lg shadow-lg overflow-hidden">
        <img :src="article.image || 'no-photo.webp'" :alt="article.title" class="w-full h-96 object-cover" />

        <div class="p-8">
          <div class="flex items-center text-gray-500 mb-4">
            <span>{{ article.date }}</span>
          </div>

          <h1 class="text-3xl font-bold text-gray-900 mb-6">
            {{ article.title }}
          </h1>

          <div class="prose max-w-none">
            <p>{{ article.content }}</p>
          </div>
        </div>
      </article>
    </div>
  </div>
</template>
