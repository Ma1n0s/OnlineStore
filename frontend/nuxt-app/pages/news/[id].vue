<script setup>
const route = useRoute()
const { id } = route.params

const {
  public: { backendUrl },
} = useRuntimeConfig()

const { data: article } = await useAsyncData(
  `news-${id}`,
  () =>
    $fetch(`${backendUrl}/api/news/${id}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
    }),
  {
    revalidate: 3600,
  }
)
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
            <p v-html="article.description"></p>
          </div>
        </div>
      </article>
    </div>
  </div>
</template>
