<script setup lang="ts">
const links = [
  { link: '/contacts', name: 'Контакты' },
  { link: '/about', name: 'О компании' },
  { link: '/news', name: 'Статьи' },
]
</script>

<template>
  <div class="bg-black text-white font-sans border-t border-gray-800 mt-auto lg:mb-0 mb-8">
    <div class="container mx-auto px-4 py-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="space-y-4">
          <NuxtLink to="/" class="inline-block">
            <NuxtImg src="full_logo.svg" class="h-20" alt="Абсолют Техно" />
          </NuxtLink>
          <p class="text-gray-400 text-xs leading-relaxed">
            Мы предлагаем широкий ассортимент качественного оборудования. У нас вы найдете всё необходимое для
            строительства, ремонта, садовых работ и многого другого. Мы гарантируем надежность и доступные цены.
          </p>
        </div>

        <div>
          <h3 class="text-base font-medium mb-4 pb-1 relative inline-block">
            Полезные ссылки
            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"></span>
          </h3>
          <ul class="space-y-2">
            <li v-for="(item, index) in links" :key="index">
              <NuxtLink
                :to="item.link || '#'"
                class="text-gray-400 hover:text-primary transition-colors duration-200 flex items-center text-sm"
                :alt="`Ссылка на ${item.name}`"
              >
                <Icon
                  name="material-symbols:chevron-right-rounded"
                  class="mr-1 text-xs opacity-70"
                  :alt="`Ссылка на ${item.name}`"
                />
                {{ item.name }}
              </NuxtLink>
            </li>
          </ul>
        </div>
        <div>
          <h3 class="text-base font-medium mb-4 pb-1 relative inline-block">
            Контакты
            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"></span>
          </h3>
          <ul class="space-y-2 text-gray-400 text-sm">
            <li class="flex items-start">
              <Icon name="mdi:map-marker-outline" class="mr-2 mt-0.5 text-sm opacity-70" />
              <span>Ул. Пушкина д.42</span>
            </li>
            <li class="flex items-start">
              <Icon name="mdi:phone-outline" class="mr-2 mt-0.5 text-sm opacity-70" />
              <span>+7 (900) 800 555 33-55</span>
            </li>
            <li class="flex items-start">
              <Icon name="mdi:email-outline" class="mr-2 mt-0.5 text-sm opacity-70" />
              <span>mail@gmail.com</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>
