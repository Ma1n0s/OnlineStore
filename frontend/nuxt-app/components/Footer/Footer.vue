<script setup lang="ts">
const links = [
  { link: 'contacts', name: 'Контакты' },
  { link: 'about', name: 'О компании' },
  { link: 'news', name: 'Статьи' },
]

const socialLinks = [
  { icon: 'i-mdi-vk', link: '#' },
  { icon: 'i-mdi-telegram', link: '#' },
  { icon: 'i-mdi-whatsapp', link: '#' },
]
</script>

<template>
  <div class="bg-black text-white font-sans">
    <div class="container mx-auto px-4 py-12">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-10 border-b border-neutral-dark">
        <div class="flex items-start">
          <div class="text-primary mr-4 mt-1">
            <i class="i-mdi-map-marker text-3xl"></i>
          </div>
          <div>
            <h4 class="text-white text-lg font-semibold mb-1">Адрес</h4>
            <p class="text-neutral-DEFAULT">Ул. Пушкина д.42</p>
            <p class="text-neutral-DEFAULT text-sm mt-1">Пн-Пт: 9:00 - 18:00</p>
          </div>
        </div>

        <div class="flex items-start">
          <div class="text-primary mr-4 mt-1">
            <i class="i-mdi-phone text-3xl"></i>
          </div>
          <div>
            <h4 class="text-white text-lg font-semibold mb-1">Телефон</h4>
            <a href="tel:+79008005533" class="text-neutral-DEFAULT hover:text-primary transition-colors"
              >+7 (900) 800 555 33-55</a
            >
            <p class="text-neutral-DEFAULT text-sm mt-1">Бесплатная консультация</p>
          </div>
        </div>

        <div class="flex items-start">
          <div class="text-primary mr-4 mt-1">
            <i class="i-mdi-email text-3xl"></i>
          </div>
          <div>
            <h4 class="text-white text-lg font-semibold mb-1">Почта</h4>
            <a href="mailto:mail@gmail.com" class="text-neutral-DEFAULT hover:text-primary transition-colors"
              >mail@gmail.com</a
            >
            <p class="text-neutral-DEFAULT text-sm mt-1">Ответим в течение дня</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-10 pt-10">
        <div class="space-y-6">
          <NuxtLink to="#" class="inline-block">
            <img src="~/public/Global_logo_test.svg" class="h-10" alt="logo" />
          </NuxtLink>
          <p class="text-neutral-DEFAULT text-sm leading-relaxed">
            Мы предлагаем широкий ассортимент качественного оборудования. У нас вы найдете всё необходимое для
            строительства, ремонта, садовых работ и многого другого. Мы гарантируем надежность и доступные цены.
          </p>

          <div class="flex space-x-4">
            <a
              v-for="(social, index) in socialLinks"
              :key="index"
              :href="social.link"
              class="text-neutral-DEFAULT hover:text-primary text-2xl transition-colors"
            >
              <i :class="social.icon"></i>
            </a>
          </div>
        </div>

        <div>
          <h3 class="text-white text-lg font-semibold mb-6 pb-2 relative inline-block">
            Полезные ссылки
            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"></span>
          </h3>
          <ul class="space-y-3">
            <li v-for="(item, index) in links" :key="index">
              <NuxtLink
                :to="item.link || '#'"
                class="text-neutral-DEFAULT hover:text-primary transition-colors flex items-center"
              >
                <i class="i-mdi-chevron-right mr-1 text-xs"></i>
                {{ item.name }}
              </NuxtLink>
            </li>
          </ul>
        </div>
      </div>

      <div class="border-t border-neutral-dark mt-12 pt-6 text-center text-neutral-DEFAULT text-sm">
        <p>© 2025 Все права защищены.<i class="i-mdi-heart text-primary"></i></p>
      </div>
    </div>
  </div>
</template>
