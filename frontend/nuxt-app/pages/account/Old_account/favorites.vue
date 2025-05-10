<script setup>
useHead({
  title: 'Избранное | Абсолют техно',
  meta: [
    {
      name: 'description',
      content:
        'Избранные товары в Абсолют техно. Сохраняйте понравившиеся товары, отслеживайте их наличие и цены. Быстрый доступ к вашим избранным позициям для удобного оформления заказа.',
    },
  ],
})
</script>
<template>
  <div class="mx-auto w-full max-w-screen-2xl px-8 space-y-8 py-8">
    <nav class="flex flex-wrap items-center gap-2 text-gray mb-4">
      <NuxtLink to="" class="hover:underline">Главная</NuxtLink>
      <span>/</span>
      <NuxtLink to="" class="font-semibold">Избранное</NuxtLink>
    </nav>

    <h1 class="text-3xl font-bold mb-6">Избранное</h1>

    <div class="flex flex-col items-center">
      <div class="w-24 h-24 mb-4">
        <Icon name="material-symbols:heart-plus" class="w-6 h-6" />
      </div>
      <p class="text-lg text-center mb-4">В избранном пока нет товаров</p>
      <p class="text-center mb-6">
        Жмите ❤️ на странице товара и добавляйте сюда то, что нравится. <br />
        Если цена на товар в избранном упадет — мы вам об этом сообщим.
      </p>
      <NuxtLink to="/" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Перейти на главную</NuxtLink>
    </div>
  </div>
</template>
